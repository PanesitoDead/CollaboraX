<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vite Manifest Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path to the Vite manifest file. This file is
    | created when the Vite CLI is run with the "build" command.
    |
    */

    'manifest_path' => public_path('build/manifest.json'),

    /*
    |--------------------------------------------------------------------------
    | Public Directory
    |--------------------------------------------------------------------------
    |
    | This value determines the public directory where your assets will be
    | served from. Typically, this is the "public" directory, but you may
    | change it if your application uses a custom structure.
    |
    */

    'public_directory' => public_path(),

    /*
    |--------------------------------------------------------------------------
    | Dev Server
    |--------------------------------------------------------------------------
    |
    | This configuration option determines whether the Vite development
    | server should be used to serve assets. If enabled, all Vite assets
    | will be loaded from the dev server instead of the public directory.
    |
    */

    'dev_server' => [
        'url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),
        'enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Build Directory
    |--------------------------------------------------------------------------
    |
    | This value determines the directory where the Vite build files are
    | located, relative to the public directory.
    |
    */

    'build_directory' => 'build',

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | This value will be used when generating asset URLs for your Vite
    | assets. You can change it if you host your assets on a CDN or
    | on a different subdomain from your application.
    |
    */

    'asset_url' => env('ASSET_URL', null),
];
