<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // admin
                if (isset($user->role) && $user->role === 'admin' && Route::has('admin.dashboard')) {
                    return redirect()->route('admin.dashboard');
                }

                // staff
                if (isset($user->role) && $user->role === 'staff' && Route::has('staff.dashboard')) {
                    return redirect()->route('staff.dashboard');
                }

                // guest or legacy 'user'
                if (isset($user->role) && in_array($user->role, ['guest','user'])) {
                    if (Route::has('guest.dashboard')) return redirect()->route('guest.dashboard');
                    if (Route::has('user.dashboard')) return redirect()->route('user.dashboard');
                    if (Route::has('dashboard')) return redirect()->route('dashboard');
                }

                // fallback
                if (Route::has('dashboard')) return redirect()->route('dashboard');
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
