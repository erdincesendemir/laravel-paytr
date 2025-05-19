<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayTR Merchant Credentials
    |--------------------------------------------------------------------------
    */

    'merchant_id' => env('PAYTR_MERCHANT_ID'),
    'merchant_key' => env('PAYTR_MERCHANT_KEY'),
    'merchant_salt' => env('PAYTR_MERCHANT_SALT'),

    /*
    |--------------------------------------------------------------------------
    | Optional Settings
    |--------------------------------------------------------------------------
    */

    'test_mode' => env('PAYTR_TEST_MODE', true),
    'debug_mode' => env('PAYTR_DEBUG_MODE', false),
];
