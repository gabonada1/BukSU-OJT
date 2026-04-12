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

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'local_bypass' => env('STRIPE_LOCAL_BYPASS', false),
        'verify_ssl' => env('STRIPE_VERIFY_SSL', true),
        'ca_bundle' => env('STRIPE_CA_BUNDLE'),
    ],

    'github' => [
        'token' => env('GITHUB_TOKEN'),
        'repository' => env('GITHUB_REPOSITORY', 'gabonada1/BukSU-Practicum'),
        'api_url' => env('GITHUB_API_URL', 'https://api.github.com'),
        'api_version' => env('GITHUB_API_VERSION', '2022-11-28'),
        'verify_ssl' => env('GITHUB_VERIFY_SSL', true),
        'ca_bundle' => env('GITHUB_CA_BUNDLE'),
    ],

];
