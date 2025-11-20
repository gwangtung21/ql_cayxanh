<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            /** @var User|null $user */
            if (! $user) {
                return redirect()->route('login');
            }

            if ($user->isAdmin()) return redirect()->route('admin.dashboard');
            if ($user->isStaff()) return redirect()->route('staff.dashboard');
            return redirect()->route('guest.dashboard');
        }
        return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function authenticated(Request $request, $user)
    {
        // admin -> admin.dashboard
        if (isset($user->role) && $user->role === 'admin' && Route::has('admin.dashboard')) {
            return redirect()->route('admin.dashboard');
        }
        // staff -> staff.dashboard
        if (isset($user->role) && $user->role === 'staff' && Route::has('staff.dashboard')) {
            return redirect()->route('staff.dashboard');
        }
        // guest or legacy 'user' -> guest.dashboard (fallbacks included)
        if (isset($user->role) && in_array($user->role, ['guest','user'])) {
            if (Route::has('guest.dashboard')) return redirect()->route('guest.dashboard');
            if (Route::has('user.dashboard')) return redirect()->route('user.dashboard');
            if (Route::has('dashboard')) return redirect()->route('dashboard');
        }
        // final fallback
        if (Route::has('dashboard')) return redirect()->route('dashboard');
        return redirect('/home');
    }
}
