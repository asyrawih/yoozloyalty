<?php

namespace App\Libraries;

use App\Models\PaymentMethod;

use Exception;

class Paypalsdk
{
    public static function payments(array $options = array())
    {
        $paymentMethod = PaymentMethod::where('blueprint', 'paypal')->first();


        $config = json_decode($paymentMethod->schema, TRUE);

        try {

            $url = $config['host'] . '/v2/checkout/orders';

            $header = array(
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("$config[client_id]:$config[secret_key]")
            );

            $post_data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [

                        'reference_id' => $options['order_id'],
                        'amount' => [
                            'currency_code' => $options['currency'],
                            'value' => number_format($options['order_amount'], 2),
                        ]
                    ]
                ],
                'application_context' => [
                    'return_url' => url('api/paypal/callback/success'),
                    'cancel_url' => url('api/paypal/callback/failed')
                ]
            ];


            $ch = curl_init(str_replace('.com//', '.com/', $url));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, TRUE);

            if (isset($response['status']) && isset($response['links'])) {

                $url = $response['links'][0]['href'];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($response, TRUE);
                return [
                    'merchant_identifier' => $response['id'],
                    'redirect' => $response['links'][1]['href']
                ];
            } else if (isset($response['error'])) {
                throw new Exception($response['error_description']);
            } else if (isset($response['details'])) {
                throw new Exception(substr($response['details'][0]['description'], 0, strpos($response['details'][0]['description'], '.')));
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
