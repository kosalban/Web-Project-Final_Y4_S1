<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        Category::create($request->validate([
            'name' => 'required|max:100|unique:categories,name',
            'description' => 'nullable|max:500',
            'status' => 'required|in:Active,Inactive',
        ]));

        return redirect()->route('categories.index')->with('success', __('Category created.'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => 'required|max:100|unique:categories,name,'.$category->id,
            'description' => 'nullable|max:500',
            'status' => 'required|in:Active,Inactive',
        ]));

        return redirect()->route('categories.index')->with('success', __('Category updated.'));
    }

    public function destroy(Category $category)
    {
        if ($category->tickets()->exists()) {
            return back()->with('error', __('This category is used by tickets and cannot be deleted.'));
        }

        $category->delete();

        return back()->with('success', __('Category deleted.'));
    }
}
