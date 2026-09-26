<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('slug', 'like', "%$q%");
            });
        }
        $products = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images']);
        return view('admin.products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'seo_content' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Product::makeUniqueSlug(Str::slug($data['name']));
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeProductImage($request->file('image'), $data['name']);
        }

        $product = Product::create($data);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $this->storeProductImage($file, $product->name);
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$product->id],
            'description' => ['nullable', 'string'],
            'seo_content' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Product::makeUniqueSlug(Str::slug($data['name']), $product->id);
        }

        if ($request->hasFile('image')) {
            // remove old file if present
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->storeProductImage($request->file('image'), $data['name']);
        }

        $product->update($data);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $this->storeProductImage($file, $product->name);
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product moved to trash.')
            ->with('deleted_product_id', $product->id);
    }

    public function restore(int $product)
    {
        $model = Product::withTrashed()->findOrFail($product);
        $model->restore();
        return redirect()->route('admin.products.index')->with('success', 'Product restored.');
    }

    public function forceDelete(int $product)
    {
        $model = Product::withTrashed()->findOrFail($product);
        \App\Models\ProductSlugRedirect::rememberCategoryFallback($model->slug, $model->category);
        if ($model->image) {
            Storage::disk('public')->delete($model->image);
        }
        $model->forceDelete();
        return redirect()->route('admin.products.index')->with('success', 'Product permanently deleted.');
    }

    public function destroyImage(Product $product, \App\Models\ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }
        if ($image->path) { Storage::disk('public')->delete($image->path); }
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }

    private function storeProductImage(\Illuminate\Http\UploadedFile $file, string $productName): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::slug($productName).'-'.Str::lower(Str::random(8)).'.'.$extension;
        return $file->storeAs('products', $filename, 'public');
    }
}
