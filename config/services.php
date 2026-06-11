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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'hitpay' => [
        'api_key' => env('HITPAY_API_KEY'),
        'api_salt' => env('HITPAY_API_SALT'),
        'sandbox' => env('HITPAY_SANDBOX', true),
        'currency' => env('HITPAY_CURRENCY', 'PHP'),
        'verify_ssl' => env('HITPAY_VERIFY_SSL', true),
        'verify_webhook' => env('HITPAY_VERIFY_WEBHOOK', true),
        'api_url' => env(
            'HITPAY_API_URL',
            env('HITPAY_SANDBOX', true)
                ? 'https://api.sandbox.hit-pay.com/v1'
                : 'https://api.hit-pay.com/v1'
        ),
    ],

];
