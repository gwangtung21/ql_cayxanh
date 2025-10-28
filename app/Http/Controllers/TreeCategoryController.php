<?php

namespace App\Http\Controllers;

use App\Models\TreeCategory;
use Illuminate\Http\Request;

class TreeCategoryController extends Controller
{
    public function index()
    {
        $categories = TreeCategory::withCount('trees')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tree_categories',
            'description' => 'nullable|string',
            'care_frequency_days' => 'required|integer|min:1'
        ]);

        TreeCategory::create($validated);
        return redirect()->route('categories.index')
                        ->with('success', 'Danh mục đã được thêm!');
    }

    public function show(TreeCategory $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(TreeCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, TreeCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tree_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'care_frequency_days' => 'required|integer|min:1'
        ]);

        $category->update($validated);
        return redirect()->route('categories.index')
                        ->with('success', 'Danh mục đã được cập nhật!');
    }

    public function destroy(TreeCategory $category)
    {
        if ($category->trees()->count() > 0) {
            return redirect()->route('categories.index')
                            ->with('error', 'Không thể xóa danh mục đang có cây!');
        }

        $category->delete();
        return redirect()->route('categories.index')
                        ->with('success', 'Danh mục đã được xóa!');
    }
}