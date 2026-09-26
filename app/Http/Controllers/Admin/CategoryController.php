<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $isTrashed = $request->boolean('trashed');

        if ($isTrashed) {
            $categories = Category::onlyTrashed()
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();
            return view('admin.categories.index', compact('categories', 'isTrashed'));
        }

        $parents = Category::whereNull('parent_id')
            ->orderBy('name')
            ->withCount(['products as products_count' => function ($q) {
                $q->withTrashed();
            }])
            ->with(['children' => function ($q) {
                $q->orderBy('name')
                    ->withCount(['products as products_count' => function ($q) {
                        $q->withTrashed();
                    }]);
            }])
            ->get();

        return view('admin.categories.index', compact('parents', 'isTrashed'));
    }

    public function create()
    {
        $parents = Category::orderBy('name')->get(['id', 'name']);
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'parent_id' => $request->input('parent_id') ?: null,
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $data['slug'] = Category::makeUniqueSlug(
            Category::normalizeSlug($data['slug'] ?: $data['name'])
        );

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->merge([
            'parent_id' => $request->input('parent_id') ?: null,
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
        ]);

        $data['slug'] = Category::makeUniqueSlug(
            Category::normalizeSlug($data['slug'] ?: $data['name']),
            $category->id
        );

        if ($request->hasFile('image')) {
            if ($category->image_path) { \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image_path); }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category moved to trash.')
            ->with('deleted_category_id', $category->id);
    }

    public function restore(int $category)
    {
        $model = Category::withTrashed()->findOrFail($category);
        $model->restore();
        return redirect()->route('admin.categories.index')->with('success', 'Category restored.');
    }

    public function forceDelete(int $category)
    {
        $model = Category::withTrashed()->findOrFail($category);
        // Guard: do not allow force delete while products exist (even trashed)
        if ($model->products()->withTrashed()->exists()) {
            return back()->with('error', 'Cannot permanently delete category with products. Remove or move products first.');
        }
        $model->forceDelete();
        return redirect()->route('admin.categories.index')->with('success', 'Category permanently deleted.');
    }
}
