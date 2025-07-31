<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        switch ($role) {
            case 'user':
                if ($user->role !== 'user') {
                    return redirect()->route('home')->with('error', 'Access denied. User role required.');
                }
                break;
                
            case 'hospital_admin':
                if ($user->role !== 'hospital_admin') {
                    return redirect()->route('home')->with('error', 'Access denied. Hospital admin role required.');
                }
                break;
                
            default:
                return redirect()->route('home')->with('error', 'Invalid role specified.');
        }

        return $next($request);
    }
} 