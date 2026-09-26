<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('admin.pages.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'category_id' => $request->input('category_id') ?: null,
        ]);
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:pages,slug'],
            'content' => ['nullable','string'],
            'featured_image' => ['nullable','image','max:4096'],
            'category_id' => ['nullable','exists:categories,id'],
            'published' => ['sometimes','boolean'],
        ]);
        if (empty($data['slug'])) { $data['slug'] = Page::makeUniqueSlug(Str::slug($data['title'])); }
        $data['published'] = $request->boolean('published', true);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('pages/featured', 'public');
        }

        Page::create($data);
        return redirect()->route('admin.pages.index')->with('success','Page created.');
    }

    public function edit(Page $page)
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('admin.pages.edit', compact('page', 'categories'));
    }

    public function update(Request $request, Page $page)
    {
        $request->merge([
            'category_id' => $request->input('category_id') ?: null,
        ]);
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:pages,slug,'.$page->id],
            'content' => ['nullable','string'],
            'featured_image' => ['nullable','image','max:4096'],
            'remove_featured_image' => ['sometimes','boolean'],
            'category_id' => ['nullable','exists:categories,id'],
            'published' => ['sometimes','boolean'],
        ]);
        if (empty($data['slug'])) { $data['slug'] = Page::makeUniqueSlug(Str::slug($data['title']), $page->id); }
        $data['published'] = $request->boolean('published', true);

        if ($request->boolean('remove_featured_image') && $page->featured_image) {
            Storage::disk('public')->delete($page->featured_image);
            $data['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('pages/featured', 'public');
        }

        $page->update($data);
        return redirect()->route('admin.pages.edit', $page)->with('success','Page updated.');
    }

    public function destroy(Page $page)
    {
        if ($page->featured_image) {
            Storage::disk('public')->delete($page->featured_image);
        }
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success','Page deleted.');
    }
}
