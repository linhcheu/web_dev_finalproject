<?php

return [
    // Patients and hospital admins log in through the API; the guard keeps
    // their account and API token in the session. This app has no database.
    'defaults' => [
        'guard' => 'web',
    ],

    'guards' => [
        'web' => [
            'driver' => 'api-session',
            'model' => App\Models\User::class,
        ],
    ],
];
