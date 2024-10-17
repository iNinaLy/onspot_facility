<?php

return [
    'paths' => ['api/*', 'flutterlogin', 'flutterregister'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', '*'],

    'allowed_origins' => [
        'http://localhost',
        'http://127.0.0.1',
        'http://10.0.2.2',
        'http://localhost:54195', // Add frontend origin
        'http://127.0.0.1:54195',
        'https://*.example.com',
        '*'
    ],
    'allowed_origins_patterns' => ['/localhost:\d+/', '/127\.0\.0\.1:\d+/', '/10\.0\.2\.2:\d+/'],
    'allowed_headers' => ['Authorization', 'Content-Type', 'Accept', '*'], // Include 'Accept'
    'exposed_headers' => ['Authorization', 'Content-Encoding', 'Content-Type'], // Expose headers as needed
    'max_age' => 3600,
    'supports_credentials' => true,
];
