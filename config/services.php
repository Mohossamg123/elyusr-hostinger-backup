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
    
    
    
    
    // paymob

'paymob' => [
    'public_key' => env('PAYMOB_PUBLIC_KEY'),
    'secret_key' => env('PAYMOB_SECRET_KEY'),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    'currency' => env('PAYMOB_CURRENCY', 'EGP'),

    'notification_url' => env('PAYMOB_NOTIFICATION_URL'),
    'redirection_url' => env('PAYMOB_REDIRECTION_URL'),
],


// kashier

'kashier' => [
    'base_url' => env(
        'KASHIER_BASE_URL',
        'https://test-api.kashier.io'
    ),

    'api_key' => env(
        'KASHIER_API_KEY'
    ),

    'secret_key' => env(
        'KASHIER_SECRET_KEY'
    ),

    'merchant_id' => env(
        'KASHIER_MERCHANT_ID'
    ),

    'currency' => env(
        'KASHIER_CURRENCY',
        'EGP'
    ),

    'allowed_methods' => env(
        'KASHIER_ALLOWED_METHODS',
        'card,wallet'
    ),

    'redirect_url' => env(
        'KASHIER_REDIRECT_URL'
    ),

    'webhook_url' => env(
        'KASHIER_WEBHOOK_URL'
    ),
],

];




