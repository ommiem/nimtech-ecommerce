<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(20)->withQueryString();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:brands,slug'],
            'website' => ['nullable','url','max:255'],
            'image' => ['nullable','image','max:4096'],
            'active' => ['sometimes','boolean'],
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Brand::makeUniqueSlug(Str::slug($data['name']));
        }
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('brands','public');
        }
        $data['active'] = $request->boolean('active', true);
        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('success','Brand created.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:brands,slug,'.$brand->id],
            'website' => ['nullable','url','max:255'],
            'image' => ['nullable','image','max:4096'],
            'active' => ['sometimes','boolean'],
        ]);
        if (empty($data['slug'])) { $data['slug'] = Brand::makeUniqueSlug(Str::slug($data['name']), $brand->id); }
        if ($request->hasFile('image')) {
            if ($brand->image_path) { \Illuminate\Support\Facades\Storage::disk('public')->delete($brand->image_path); }
            $data['image_path'] = $request->file('image')->store('brands','public');
        }
        $data['active'] = $request->boolean('active', true);
        $brand->update($data);
        return redirect()->route('admin.brands.index')->with('success','Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return back()->with('success','Brand deleted.');
    }
}
