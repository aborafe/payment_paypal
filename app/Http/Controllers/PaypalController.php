<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;
class PaypalController extends Controller
{
    public function payment()
    {
        $data = [];
        $data['items'] = [
            [
                'name' => 'Product 1',
                'price' => 9.99,
                'qty' => 1
            ],
            [
                'name' => 'Product 2',
                'price' => 4.99,
                'qty' => 2
            ]
        ];
        
        $data['invoice_id'] = 1;
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = route('paypal.success');
        $data['cancel_url'] = route('paypal.cancel');
        
        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }
        
        $data['total'] = $total;
        $provider = new ExpressCheckout;
        $response = $provider->setExpressCheckout($data);
        // dd($response);
        return redirect($response['paypal_link']);
        //give a discount of 10% of the order amount
        // $data['shipping_discount'] = round((10 / 100) * $total, 2);

    }

    public function success(Request $request)
    {
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        if(in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            // Payment successful
            return response()->json(['message' => 'Payment successful']);
        }
        // Payment failed
        return response()->json(['message' => 'Payment failed'], 402);
    }

    public function cancel()
    {
        // Payment cancelled
        return response()->json(['message' => 'Payment cancelled'], 402);
    }

}
