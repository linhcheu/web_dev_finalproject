<?php

namespace App\Providers;

use App\Services\Api\ApiClient;
use App\Services\Api\ApiSessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiClient::class);
    }

    public function boot(): void
    {
        // "api-session" guard: the account + API token live in the session (see config/auth.php).
        Auth::extend('api-session', fn ($app, string $name, array $config) => new ApiSessionGuard(
            $name,
            $config['model'],
            $app['session.store'],
        ));
    }
}
