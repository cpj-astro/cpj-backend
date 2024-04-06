<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Errors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\User;
use App\Models\MatchAstrology;
use App\Models\AstrologyData;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Matches;

class PaymentController extends Controller
{
    use CommonTraits;
    private $base_url, $salt_key, $merchant_id, $key_index;
    public function __construct()
    {
        if(config('app.env') == 'production') {
            $this->node_url = config('services.node.production.base_url');
            $this->base_url = config('services.phonepe.production.base_url');
            $this->salt_key = config('services.phonepe.production.salt_key');
            $this->key_index = config('services.phonepe.production.key_index');
            $this->merchant_id = config('services.phonepe.production.merchant_id');
        } else {
            $this->node_url = config('services.node.sandbox.base_url');
            $this->base_url = config('services.phonepe.sandbox.base_url');
            $this->salt_key = config('services.phonepe.sandbox.salt_key');
            $this->key_index = config('services.phonepe.sandbox.key_index');
            $this->merchant_id = config('services.phonepe.sandbox.merchant_id');
        }
    }
    
    public function phonepePay(Request $request) {
        try {
            $data = $request->all();
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'match_id' => $data['matchId'],
                'pandit_id' => $data['panditId'],
                'merchant_transaction_id' => $data['merchantTransactionId'],
                'payment_instrument' => json_encode($data['paymentInstrument']),
                'amount' => $data['amount'] / 100,
                'status' => 'pending'
            ]);
            $payload = base64_encode(json_encode($data));
            $createChecksum = $payload."/pg/v1/pay".$this->salt_key;
            $checksum = hash('sha256', $createChecksum);
            $checksum = $checksum.'###1';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->base_url.'pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'request' => $payload
                ]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "X-VERIFY: ".$checksum,
                    "accept: application/json"
                ],
            ));
    
            $response = curl_exec($curl);
    
            if (curl_errno($curl)) {     
                $error_msg = curl_error($curl); 
                Log::info($error_msg);
            } 
            
            curl_close($curl);

            $responseData = json_decode($response, true);

            $url = null;
            if($responseData && isset($responseData['data']) && isset($responseData['data']['instrumentResponse']) && isset($responseData['data']['instrumentResponse']['redirectInfo']) && isset($responseData['data']['instrumentResponse']['redirectInfo']['url'])) {
                $url = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
            }

            return response()->json(['status' => true, 'url' => $url ,'Credentials Verified! Moving for payment'], 200);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function phonepeStatus($match_id, $user_id, $merchant_transaction_id) {
        try {
            if(isset($match_id) && isset($merchant_transaction_id)) {
                $matchStatus = Matches::where('match_id', $match_id)->first();
                if(!$matchStatus) {
                    return view('status', compact('match_id', 'merchant_transaction_id'));
                }
            }
            $payload = "/pg/v1/status/".$this->merchant_id."/".$merchant_transaction_id.$this->salt_key;
            $checksum = hash('sha256', $payload);
            $checksum = $checksum.'###1';

            $curl = curl_init();
    
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->base_url.'status/'.$this->merchant_id."/".$merchant_transaction_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "X-VERIFY: ".$checksum,
                    "X-MERCHANT-ID: ".$this->merchant_id,
                    "accept: application/json"
                ],
            ));
    
            $response = curl_exec($curl);
            
            if (curl_errno($curl)) {     
                $error_msg = curl_error($curl); 
                Log::info($error_msg);
            } 
            
            curl_close($curl);

            $responseData = json_decode($response, true);
            
            if (isset($responseData['success']) && $responseData['success'] && $responseData['data'] && $responseData['data']['merchantTransactionId']) {
                $payment = Payment::where('merchant_transaction_id', $merchant_transaction_id)->first();
                if ($payment) {
                    $userData = User::where('id', $user_id)->first();
                    if ($userData) {
                        $userData->report_counter = $userData->report_counter + 1;
                        $userData->save();
                    }
                    $payment->status = 'success';
                    $payment->transaction_id = $responseData['data']['transactionId'];
                    $payment->payment_instrument = json_encode($responseData['data']);
                    $payment->save();

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $this->node_url.'subscribe-match',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => 'match_id='.$match_id.'&user_id='.$user_id,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/x-www-form-urlencoded'
                        ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                }
                return view('payment-success', compact('match_id', 'merchant_transaction_id'));
            } else {
                $payment = Payment::where('merchant_transaction_id', $merchant_transaction_id)->first();
                if ($payment) {
                    $payment->status = 'failed';
                    $payment->save();
                }
                return view('payment-failed', compact('match_id', 'merchant_transaction_id'));
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $order = $api->order->create(array(
                'amount' => $request->post('amount') * 100, // Amount  in paise (1 INR = 100 paise)
                'currency' => 'INR',
                'receipt' => uniqid(),
                'notes' => [
                    'user_id' => 100, // Pass the user's ID => who pay for it
                    'pandit_id' => 200, // Pass the pandit's ID
                ],
            ));
            
            return response()->json(['order_id' => $order->id]);
        } catch (SignatureVerificationError $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function capturePayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        
        try {
            $attributes = [
                'razorpay_order_id' => $request->post('razorpayOrderId'),
                'razorpay_payment_id' => $request->post('razorpayPaymentId'),
                'razorpay_signature' => $request->post('razorpaySignature'),
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $paymentDetails = $api->payment->fetch($request->post('razorpayPaymentId'));
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'match_id' => $request->post('match_id'),
                'pandit_id' => $request->post('pandit_id'),
                'razorpay_order_id' => $request->post('razorpayOrderId'),
                'razorpay_payment_id' => $request->post('razorpayPaymentId'),
                'razorpay_signature' => $request->post('razorpaySignature'),
                'amount' => $request->post('amount'),
                'status' => 'success'
            ]);

            // if payment successful then create match astrology
            if($payment->status == 'success') {
                $houses = [
                    'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo',
                    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces',
                ];
                
                $matchAstrologyCreate = null;

                if ($request->post('moon_sign')) {
                    $moonSign = intval($request->post('moon_sign'));
                    if (in_array($moonSign, range(1, 12))) {
                        $zodiacSign = $houses[$moonSign - 1];
                        if ($zodiacSign !== false) {
                            $fetchAstrologyData = AstrologyData::select($zodiacSign)
                            ->where('match_id', $request->post('match_id'))
                            ->first();

                            if($fetchAstrologyData) {
                                $data = json_decode($fetchAstrologyData, true);
                                if ($data !== null) {
                                    $firstValue = $data[$zodiacSign];
                                } else {
                                    $firstValue = "";
                                }
                                $matchAstrologyCreate = MatchAstrology::create([
                                    'user_id' => auth()->id(),
                                    'match_id' => $request->post('match_id'),
                                    'astrology_data' => $firstValue
                                ]); 
                            }
                        }
                    }
                }
            }
            
            return response()->json(['msg' => 'Payment Received & Match Astrology Created Successfully', 'success' => true]);
        } catch (SignatureVerificationError $e) {
            return response()->json(['error' => $e, 'success' => false], 200);
        }
    }
}
