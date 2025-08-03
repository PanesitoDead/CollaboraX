<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for external APIs
    | and the API configuration
    |
    */

    'planes' => [
        'base_url' => env('PLANES_API_BASE_URL', 'http://34.173.216.37:3000/api'),
        'timeout' => env('PLANES_API_TIMEOUT', 30),
    ],
];
