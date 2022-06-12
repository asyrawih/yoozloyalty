<?php

namespace App\Libraries;
use App\Models\PaymentMethod;

use Exception;

class Yoozpgsdk
{
    public static function payments(array $options = array())
    {
        // $mode = config('yooz.mode');

        // $config = config("yooz.{$mode}");
        $paymentMethod = PaymentMethod::where('blueprint', 'yooz_pg')->first();
        $config = json_decode($paymentMethod->schema, TRUE);

        $merchantId = $config['merchant_id'];

        $encryptionKey = $config['encryption_key'];

        $secretKey = $config['secret_key'];

        $host = $config['host'];

        $cryptoJS = new Crypto();

        $data = [
            'merchant_id' => $merchantId,
            'order_id' => $options['order_id'],
            'order_amount' => $options['order_amount'],
            'currency' => $options['currency'],
            'customer_name' => $options['customer_name'],
            'customer_phone' => $options['customer_phone'],
            'customer_email' => $options['customer_email'],
            'language' => 'EN',
            'api_option' => 'hosted',
            'success_url' => url("api/yoozpg/callback/success"),
            'failed_url' => url("api/yoozpg/callback/failed"),
            'billing_details' => [
                'name' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'phone' => '',
                'email' => '',
            ],
            'shipping_details' => [
                'name' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'phone' => '',
                'email' => '',
            ],
            'udf1' => '',
            'udf2' => '',
            'udf3' => '',
            'udf4' => '',
            'udf5' => '',
            'udf6' => '',
        ];

        $encData = base64_encode($cryptoJS->encrypt($encryptionKey, json_encode($data)));

        $payload = [
            'merchant_id' => $merchantId,
            'enc_data' => $encData,
        ];

        try {
            set_time_limit(0);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "{$host}/payments",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 1000000,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "X-Requested-With: XMLHttpRequest",
                    "Authorization: Bearer $secretKey"
                ]
            ]);

            /*
            |----------------------------------------------------------------
            | Get Response
            |----------------------------------------------------------------
            */
            $response = curl_exec($curl);

            /*
            |----------------------------------------------------------------
            | Close CURL Process
            |----------------------------------------------------------------
            */
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $response = json_decode($response, true);

            if ($response['success'] && isset($response['data']['redirect'])) {
                return $response['data'];
            }

            throw new Exception($response['message']);

        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
