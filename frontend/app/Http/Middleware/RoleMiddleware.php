<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * role:user or role:hospital_admin. Send people who are logged in with the
 * wrong kind of account back home. (The API enforces the same rule.)
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()?->role !== $role) {
            return redirect()->route('home')->with('error', 'You do not have access to that page.');
        }

        return $next($request);
    }
}
