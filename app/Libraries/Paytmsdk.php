<?php

namespace App\Libraries;
use App\Models\PaymentMethod;

use App\Libraries\PaytmChecksum;
use Exception;

Class Paytmsdk {
    public static function payments(array $options = array())
    {
        $paymentMethod = PaymentMethod::where('blueprint', 'paytm')->first();
        $config = json_decode($paymentMethod->schema, TRUE);

        $paytmParams = array();
        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => $config['mid'],
            "websiteName"   => $config['website'],
            "orderId"       => $options['order_id'],
            "callbackUrl"   => url("api/paytm/callback"),
            "txnAmount"     => array(
                    "value"     => $options['order_amount'],
                    "currency"  => $options['currency'],
                ),
            "userInfo"      => array(
                    "custId"    => $options['customer_id'],
                    "mobile"    => $options['customer_phone'],
                    "email"     => $options['customer_email'],
                    "firstName" => explode(' ', $options['customer_name'])[0]
                ),
        );

        try {

            /*
            * Generate checksum by parameters we have in body
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $checksum = PaytmChecksum::generateSignature(
                json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES),
                $config['merchant_key']
            );

            $paytmParams["head"] = array(
                "signature" => $checksum
            );
            
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            /* for Staging */
            // $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";
            $url = $config['host'] . "/initiateTransaction?mid=$config[mid]&orderId=$options[order_id]";
            
            /* for Production */
            // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, TRUE);

            if($response['body']['resultInfo']['resultStatus'] == 'S') {
                $txnToken = $response['body']['txnToken'];
                $post_data = [
                    'mid' => $config['mid'],
                    'orderId' => $options['order_id'],
                    'txnToken' => $txnToken
                ];
                
                $url = $config['host'] . "showPaymentPage?mid=$config[mid]&orderId=$options[order_id]";
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded")); 
                $response = curl_exec($ch);
                curl_close($ch);

                return [
                    'html' => $response
                ];
            } else {
                throw new Exception($response['body']['resultInfo']['resultMsg']);
            }

        } catch (Exception $exception) {
            throw $exception;
        }
        
    }
}