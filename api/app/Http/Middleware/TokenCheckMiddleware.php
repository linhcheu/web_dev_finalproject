<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log token details for debugging when needed
        if (config('app.debug') && $request->bearerToken()) {
            \Log::info('Token usage', [
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'token_prefix' => substr($request->bearerToken(), 0, 10) . '...',
                'user_id' => $request->user() ? $request->user()->user_id : 'not authenticated'
            ]);
        }

        return $next($request);
    }
}
