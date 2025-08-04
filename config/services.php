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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Microservicio de Suscripciones/Pagos
    |--------------------------------------------------------------------------
    |
    | Configuración para el microservicio que maneja las suscripciones y pagos
    | con Mercado Pago. Este microservicio reemplaza el uso del campo obsoleto
    | plan_servicio_id en la tabla empresas.
    |
    */
    'suscripciones' => [
        'url' => env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000'),
        'api_key' => env('PAGOS_MICROSERVICE_API_KEY', 'default-api-key'),
        'timeout' => env('PAGOS_MICROSERVICE_TIMEOUT', 30),
    ],

];
