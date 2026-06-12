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
     * Verifies that the authenticated user has the required role.
     * Redirects to login if unauthenticated, or to their respective
     * dashboard if the role does not match.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            // Redirect to the correct panel based on actual role
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.companies.index')
                : redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}
