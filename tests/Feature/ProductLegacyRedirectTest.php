<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductLegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_product_slug_redirects_to_replacement_product(): void
    {
        $category = Category::create([
            'name' => 'Phones',
            'slug' => 'phones',
        ]);

        $replacement = Product::create([
            'category_id' => $category->id,
            'name' => 'Google Pixel 6 Pro 256GB',
            'slug' => 'google-pixel-6-pro-256gb',
            'price' => 62000,
            'stock' => 2,
        ]);

        ProductSlugRedirect::create([
            'old_slug' => 'google-pixel-6-pro-128gb',
            'product_id' => $replacement->id,
        ]);

        $this->get('/products/google-pixel-6-pro-128gb')
            ->assertRedirect(route('products.show', ['productSlug' => $replacement->canonical_slug]));
    }

    public function test_deleted_product_slug_redirects_to_category_page(): void
    {
        $category = Category::create([
            'name' => 'TVs',
            'slug' => 'tvs',
        ]);

        $deletedProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Vitron 65 Smart 4K HDR Frameless TV',
            'slug' => 'vitron-65-smart-4k-v6588-hdr-frameless-tv',
            'price' => 74999,
            'stock' => 0,
        ]);

        $deletedProduct->delete();

        $this->get('/products/vitron-65-smart-4k-v6588-hdr-frameless-tv')
            ->assertRedirect(route('categories.show', ['category' => $category->canonical_slug]));

        $this->assertDatabaseHas('product_slug_redirects', [
            'old_slug' => 'vitron-65-smart-4k-v6588-hdr-frameless-tv',
            'category_id' => $category->id,
        ]);
    }

    public function test_mixed_case_product_slug_redirects_to_canonical_slug(): void
    {
        $category = Category::create([
            'name' => 'Phones',
            'slug' => 'phones',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Oppo A3X 6/128GB',
            'slug' => 'oppo-A3-4g-128gb',
            'price' => 19999,
            'stock' => 4,
        ]);

        $this->get('/products/oppo-A3-4g-128gb')
            ->assertRedirect(route('products.show', ['productSlug' => 'oppo-a3-4g-128gb']));
    }

    public function test_legacy_product_category_redirects_to_matching_category(): void
    {
        $category = Category::create([
            'name' => 'Speakers',
            'slug' => 'speakers',
        ]);

        $this->get('/product-category/speaker/')
            ->assertRedirect(route('categories.show', ['category' => $category->canonical_slug]));
    }

    public function test_unknown_legacy_product_category_stays_404(): void
    {
        $this->get('/product-category/not-a-real-category/')
            ->assertNotFound();
    }

    public function test_product_schema_includes_brand_delivery_time_and_return_fields(): void
    {
        $category = Category::create([
            'name' => 'Phones',
            'slug' => 'phones',
        ]);

        $brand = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Apple iPhone 18 Pro Max',
            'slug' => 'apple-iphone-18-pro-max',
            'price' => 182000,
            'stock' => 0,
        ]);

        $this->get(route('products.show', ['productSlug' => $product->canonical_slug]))
            ->assertOk()
            ->assertSee('"@type":"Brand","name":"Apple"', false)
            ->assertSee('"deliveryTime":{"@type":"ShippingDeliveryTime"', false)
            ->assertSee('"returnMethod":"https://schema.org/ReturnByMail"', false)
            ->assertSee('"returnFees":"https://schema.org/FreeReturn"', false)
            ->assertSee('"sku":"'.$product->id.'"', false);
    }
}
