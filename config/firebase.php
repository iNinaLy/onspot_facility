<?php

return [
    'credentials' => [
        'file' => storage_path('app/onspot-40f9a-firebase-adminsdk-ka4vw-d3017e3bc2.json'),
    ],

    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'), // Add this in your .env file if using Firebase Realtime Database
    ],

    'messaging' => [
        'default_notification' => [
            'title' => env('FIREBASE_NOTIFICATION_TITLE', 'Default Title'),
            'body' => env('FIREBASE_NOTIFICATION_BODY', 'Default Body'),
            'image' => env('FIREBASE_NOTIFICATION_IMAGE', null),
        ],
    ],
];
