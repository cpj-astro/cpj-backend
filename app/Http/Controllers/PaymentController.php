<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Errors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\MatchAstrology;
use App\Models\AstrologyData;

class PaymentController extends Controller
{
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
            \Log::error('Signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => $e, 'success' => false], 200);
        }
    }
}
