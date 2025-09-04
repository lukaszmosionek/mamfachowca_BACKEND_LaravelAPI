<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://mamfachowca.mosioneklukasz.pl'),
        env('FRONTEND_URL_2', 'http://www.mamfachowca.mosioneklukasz.pl'),
        env('FRONTEND_URL_3', 'https://mamfachowca.mosioneklukasz.pl'),
        env('FRONTEND_URL_4', 'https://www.mamfachowca.mosioneklukasz.pl'),
        env('FRONTEND_URL_5'),
        env('FRONTEND_URL_6'),
    ], // port Vue

    'allowed_methods' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
