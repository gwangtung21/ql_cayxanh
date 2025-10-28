<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,staff')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        // If user is not logged in, redirect to login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // If no roles specified, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Support both comma and pipe separators, trim values
        $allowed = array_map('trim', preg_split('/[|,]/', $roles));

        // If user's role is missing or not allowed, deny access
        $userRole = $user->role ?? null;
        if (! $userRole || ! in_array($userRole, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
