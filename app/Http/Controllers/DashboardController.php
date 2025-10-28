<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Models\TreeCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Redirect to role-specific dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        // fallback
        return redirect()->route('login');
    }
}