<?php

/**
 * Yooz Payment Gataway Configuration
 */

return [
    'mode' => env('YOOZ_PG_MODE', 'test'),
    'test' => [
        'host' => env('YOOZ_PG_HOST_TEST', 'https://test.yoozpayments.net/api/v2'),
        'merchantId' => env('YOOG_PG_MERCHANT_ID', NULL),
        'encryptionKey' => env('YOOZ_PG_ENCRYPTION_KEY', ''),
        'secretKey' => env('YOOZ_PG_SECRET_KEY', ''),
    ],
    'live' => [
        'host' => env('YOOZ_PG_HOST_LIVE', 'https://live.yoozpayments.net/api/v2'),
        'merchantId' => env('YOOG_PG_MERCHANT_ID', NULL),
        'encryptionKey' => env('YOOZ_PG_ENCRYPTION_KEY', ''),
        'secretKey' => env('YOOZ_PG_SECRET_KEY', ''),
    ],
];
