<?php namespace Applications\School\Modules\Teacher\Payment;

use Library\BackController;

class PaymentController extends BackController
{
    public function index()
    {

    }

    public function test()
    {
        \Stripe\Stripe::setApiKey("sk_test_dVsKknKBGTxmMO87Aah9MBzn");

// Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

// Create the charge on Stripe's servers - this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                    "amount" => 1000, // amount in cents, again
                    "currency" => "cad",
                    "source" => $token,
                    "description" => "payinguser@example.com")
            );
            echo 'success';
        } catch(\Stripe\Error\Card $e) {
            echo 'card declined';
        }
    }
}