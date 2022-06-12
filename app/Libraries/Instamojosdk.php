<?php

namespace App\Libraries;
use App\Models\PaymentMethod;

use Exception;

class Instamojosdk
{
    public static function payments(array $options = array())
    {
        $paymentMethod = PaymentMethod::where('blueprint', 'instamojo')->first();
        $config = json_decode($paymentMethod->schema, TRUE);

        $host = $config['host'];
        $api_key = $config['api_key'];
        $auth_token = $config['auth_token'];

        $data = [
            'purpose' => $options['order_id'],
            'amount' => $options['order_amount'],
            'phone' => $options['customer_phone'],
            'buyer_name' => $options['customer_name'],
            'redirect_url' => url("api/instamojo/callback"),
            'send_email' => false,
            'webhook' => url("api/instamojo/webhook"),
            'send_sms' => false,
            'email' => $options['customer_email'],
            'allow_repeated_payments' => true,
        ];

        $url = $host . "/payment-requests/";
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "X-Api-Key: $api_key",
                "X-Auth-Token: $auth_token"
            ));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            
            $response = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($response, true);

            if ($response['success']) {
                return [
                    'redirect' => $response['payment_request']['longurl']
                ];
            }

            // Get the first error message
            throw new Exception($response['message'][array_keys($response['message'])[0]][0]);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public static function details($paymentRequestId, $paymentId) {
        $paymentMethod = PaymentMethod::where('blueprint', 'instamojo')->first();
        $config = json_decode($paymentMethod->schema, TRUE);

        $host = $config['host'];
        $api_key = $config['api_key'];
        $auth_token = $config['auth_token'];

        $url = $host . "/payment-requests/$paymentRequestId/$paymentId";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Api-Key: $api_key",
            "X-Auth-Token: $auth_token"
        ));
        curl_setopt($curl, CURLOPT_POST, true);
            
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, TRUE);
    }
}
