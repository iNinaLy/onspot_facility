<?php

return [

    'paths' => ['api/*'], // The paths you want to apply CORS to

    'allowed_methods' => ['*'], // Allows all HTTP methods (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => ['*'], // Allows requests from any origin (for testing purposes)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allows all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Set to true if you need to include cookies in requests

];
