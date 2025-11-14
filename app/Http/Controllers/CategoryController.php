<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'children', 'assets'])
            ->orderBy('name')
            ->get();
            
        $mainCategories = Category::whereNull('parent_id')->get();
        
        return view('categories.index', compact('categories', 'mainCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('categories')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
            
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        // Prevent category from being its own parent
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Category cannot be its own parent.'])->withInput();
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('categories')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if category has assets
        if ($category->assets()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with associated assets.']);
        }

        // Check if category has subcategories
        if ($category->children()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with subcategories.']);
        }

        $category->delete();

        return redirect()->route('categories')->with('success', 'Category deleted successfully.');
    }
}