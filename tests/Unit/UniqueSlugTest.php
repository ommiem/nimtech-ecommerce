<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UniqueSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_unique_slug_considers_soft_deleted_rows(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'My Product',
            'slug' => 'my-product',
            'price' => 9.99,
            'stock' => 1,
        ]);

        $product->delete();

        $this->assertSame('my-product-2', Product::makeUniqueSlug(Str::slug('My Product')));
    }

    public function test_unique_slug_can_ignore_current_model_id(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'My Product',
            'slug' => 'my-product',
            'price' => 9.99,
            'stock' => 1,
        ]);

        $this->assertSame('my-product', Product::makeUniqueSlug('my-product', $product->id));
    }
}

