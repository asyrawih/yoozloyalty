<?php

namespace App\Libraries;
use App\Models\PaymentMethod;

use App\Libraries\PaytmChecksum;
use Exception;

Class Payusdk {
    public static function payments(array $options = array())
    {
        $paymentMethod = PaymentMethod::where('blueprint', 'payu')->first();
        $config = json_decode($paymentMethod->schema, TRUE);

        $splitName = explode(' ', $options['customer_name']);

        $data = array(
            'url' => $config['host'] . '/_payment',
            'key' => $config['merchant_key'],
            'salt' => $config['salt'],
            'txnid' => $options['order_id'],
            'amount' => $options['order_amount'],
            'productinfo' => $options['product_info'],
            'firstname' => $splitName[0],
            'email' => $options['customer_email'],
            'phone' => $options['customer_phone'],
            'surl' => url("api/payu/callback/success"),
            'furl' => url("api/payu/callback/failed"),
            'curl' => url("api/payu/callback/cancel")
        );

        if (isset($splitName[1]))
            $data['lastname'] = $splitName[1];

        $data['hash'] = strtolower(
            hash(
                'sha512',
                "$data[key]|$data[txnid]|$data[amount]|$data[productinfo]|$data[firstname]|$data[email]|||||||||||$data[salt]"
            )
        );

        return [
            'html' => view('payu.payment', $data)->render()
        ];
    }
}