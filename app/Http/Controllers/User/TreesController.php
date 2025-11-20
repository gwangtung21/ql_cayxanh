<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TreesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Kiểm tra xem người dùng có vai trò 'staff' không
        if ($user && $user->role === 'staff') {
            // Nếu là staff, chỉ hiển thị những cây được phân công cho họ
            $trees = Tree::where('assigned_to', $user->id)->get();
        } else {
            // Nếu không phải staff, hiển thị tất cả các cây (admin hoặc guest có thể xem tất cả cây)
            $trees = Tree::all();
        }

        return view('guest.trees_index', compact('trees')); // đổi từ 'user.trees_index'
    }

    // Các phương thức khác của controller
}