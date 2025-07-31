<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Workaround for cURL error 60 on Windows/Wamp
        if (app()->environment('local')) {
            putenv('CURL_CA_BUNDLE=C:/wamp64/bin/php/php8.3.14/cacert.pem');
        }
    }
}
