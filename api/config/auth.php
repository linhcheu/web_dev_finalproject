<?php

return [
    // The API is stateless: requests authenticate with Sanctum bearer tokens
    // issued to App\Models\User (patients, hospital admins) or App\Models\Admin.
    'defaults' => [
        'guard' => 'sanctum',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => ['driver' => 'session', 'provider' => 'users'],
    ],

    'providers' => [
        'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
        'admins' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
