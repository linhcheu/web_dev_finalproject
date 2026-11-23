<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to one kind of token owner:
 *   account:admin            platform admins (admin panel)
 *   account:member           any patient / hospital admin
 *   account:user             patients only
 *   account:hospital_admin   hospital administrators only
 */
class EnsureAccountType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $actor = $request->user();

        $allowed = match ($type) {
            'admin' => $actor instanceof Admin,
            'member' => $actor instanceof User && $actor->is_active,
            default => $actor instanceof User && $actor->is_active && $actor->role === $type,
        };

        abort_unless($allowed, 403, 'This action is not allowed for your account.');

        return $next($request);
    }
}
