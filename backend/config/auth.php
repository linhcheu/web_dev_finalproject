<?php

return [
    // Platform admins log in through the API; the guard keeps their account and
    // API token in the session. This app has no database.
    'defaults' => [
        'guard' => 'admin',
    ],

    'guards' => [
        'admin' => [
            'driver' => 'api-session',
            'model' => App\Models\Admin::class,
        ],
        // Nobody else logs in to the admin panel; kept so Auth::guard('web') in views returns null.
        'web' => [
            'driver' => 'api-session',
            'model' => App\Models\User::class,
        ],
    ],
];
