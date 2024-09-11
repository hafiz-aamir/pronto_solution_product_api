<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;


class StripePaymentController extends Controller
{
    
    public function pay_via_stripe(){

        // Stripe secret key ko set karein
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Stripe payment intent ko create karein
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            return response()->json([
                'paymentIntent' => $paymentIntent,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }

    }


}
