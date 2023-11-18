<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Errors;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $order = $api->order->create(array(
                'amount' => 1000, // Amount  in paise (1 INR = 100 paise)
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
        dd($request);
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            // $attributes = [
            //     'razorpay_order_id' => $request->post('razorpayOrderId'),
            //     'razorpay_payment_id' => $request->post('razorpayPaymentId'),
            //     'razorpay_signature' => $request->post('razorpaySignature'),
            // ];

            // dd($api->utility->verifyPaymentSignature($attributes));

            // $paymentDetails = $api->payment->fetch($request->post('razorpay_payment_id'));
            // return response()->json(['paymentDetails' => $paymentDetails]);
        } catch (SignatureVerificationError $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
