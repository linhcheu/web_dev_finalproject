<?php

return [
    // Platform admin created by `php artisan db:seed` (see AdminSeeder).
    // Read through config() so it still works after `php artisan optimize` caches the config.
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@careconnect.com'),
        'password' => env('ADMIN_PASSWORD', 'admin123'),
    ],
];
