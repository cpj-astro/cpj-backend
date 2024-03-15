<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'cricket-champion' => [
        'endpoint' => env('CRICKET_CHAMPION_ENDPOINT'),
        'token' => env('CRICKET_CHAMPION_TOKEN')
    ],
    
    'fcm_notification_token' => env('FCM_NOTIFICATION_TOKEN'),
    
    'node' => [
        'production' => [
            'base_url' => env('NODE_PROD_ENDPOINT'),
        ],
        'sandbox' => [
            'base_url' => env('NODE_SANDBOX_ENDPOINT'),
        ]
    ],

    'phonepe' => [
        'production' => [
            'base_url' => env('PHONEPE_PROD_ENDPOINT'),
            'merchant_id' => env('PHONEPE_PROD_MERCHANT_ID'),
            'key_index' => env('PHONEPE_PROD_KEY_INDEX'),
            'salt_key' => env('PHONEPE_PROD_SALT_KEY'),
        ],
        'sandbox' => [
            'base_url' => env('PHONEPE_SANDBOX_ENDPOINT'),
            'merchant_id' => env('PHONEPE_SANDBOX_MERCHANT_ID'),
            'key_index' => env('PHONEPE_SANDBOX_KEY_INDEX'),
            'salt_key' => env('PHONEPE_SANDBOX_SALT_KEY'),
        ]
    ]
];
