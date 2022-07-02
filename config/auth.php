<?php

return [

    'jwt' => [
        'enabled' => true
    ],

    'accesstoken' => [
        'enabled' => true
    ],

    'hmac' => [
        'enabled' => true
    ],

    'sessions' => [
        'enabled' => env('CRATE_ENV', 'production') === 'development'
    ],

    'basic' => [
        'enabled' => false
    ]

];
