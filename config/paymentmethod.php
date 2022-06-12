<?php

return [
    'plan_expires' => [
        /**
         * Available interval: minute, day
         */
        'interval' => env('PAYMENT_INTERVAL_PLAN_EXPIRES', 'day'),

        /**
         * Interval Value
         */
        'value' => env('PAYMENT_INTERVAL_VALUE_PLAN_EXPIRES', 30),
    ],
    'invoice_expires' => [
        /**
         * Available interval: minute, day
         */
        'interval' => env('PAYMENT_INTERVAL_INVOICE_EXPIRES', 'day'),

        /**
         * Interval Value
         */
        'value' => env('PAYMENT_INTERVAL_VALUE_INVOICE_EXPIRES', 7),
    ],
];
