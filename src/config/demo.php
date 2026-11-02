<?php

return [
    'accounts' => [
        'admin' => [
            'name' => 'Demo Admin',
            'email' => env('DEMO_ADMIN_EMAIL'),
            'password' => env('DEMO_ADMIN_PASSWORD'),
        ],
        'user' => [
            'name' => 'Demo User',
            'email' => env('DEMO_USER_EMAIL'),
            'password' => env('DEMO_USER_PASSWORD'),
        ],
    ],
];
