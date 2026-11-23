<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // The website and admin panel call the API from the server, so every request
        // shares one IP. Limit login attempts per e-mail address instead of per IP.
        RateLimiter::for('auth', fn (Request $request) => [
            Limit::perMinute(5)->by('auth:'.strtolower((string) $request->input('email'))),
            Limit::perMinute(60)->by('auth-ip:'.$request->ip()),
        ]);

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(300)
            ->by($request->bearerToken() ? sha1($request->bearerToken()) : $request->ip()));
    }
}
