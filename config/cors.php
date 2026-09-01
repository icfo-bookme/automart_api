<?php

return [
    'paths' => ['api/*', 'login', 'logout', 'register', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',     // Local Next.js app URL
        'http://187.52.116.124:3000',
        'https://automart.com.bd',   // Production frontend
        'https://www.automart.com.bd',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];