<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;

class TreesController extends Controller {
    // ...existing code...

    public function index()
    {
        $trees = Tree::all(); // or your existing query logic
        return view('guest.trees_index', compact('trees')); // đổi từ 'user.trees_index'
    }

    // ...existing code...
}