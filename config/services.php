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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'firebase' => [
    'credentials' => storage_path('app/onspot-40f9a-firebase-adminsdk-ka4vw-d3017e3bc2.json'),
    ],


        'supabase' => [
            'url' => env('https://ghfcpddpywmathkhmkff.supabase.co'),
            'key' => env('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdoZmNwZGRweXdtYXRoa2hta2ZmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzQzMTk5NTcsImV4cCI6MjA0OTg5NTk1N30.pD09VuhLHIjww0hIbCbltJL9IvFyxZZp0ipfcswUIy0'),
        ],
    


];
