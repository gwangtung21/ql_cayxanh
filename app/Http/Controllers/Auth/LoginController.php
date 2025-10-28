<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
            return redirect()->route('student.dashboard');
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
}
