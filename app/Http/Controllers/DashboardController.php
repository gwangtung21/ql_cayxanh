<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
	public function index(Request $request)
	{
		$user = Auth::user();
		if (!$user) {
			return redirect()->route('login');
		}

		$role = strtolower((string)($user->role ?? ''));

		if ($role === 'admin' && Route::has('admin.dashboard')) {
			return redirect()->route('admin.dashboard');
		}

		if ($role === 'staff' && Route::has('staff.dashboard')) {
			return redirect()->route('staff.dashboard');
		}

		// For 'user' role: return the view directly to avoid redirect loops
		if ($role === 'user') {
			Log::debug('DashboardController@index: serving user.dashboard view for user id '.$user->id);
			return view('user.dashboard');
		}

		// If user.dashboard route exists but role wasn't 'user', try redirect as fallback
		if (Route::has('user.dashboard')) {
			return redirect()->route('user.dashboard');
		}

		// final safe fallback: show user view
		return view('user.dashboard');
	}
}