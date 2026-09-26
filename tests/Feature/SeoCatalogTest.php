<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create(['site_name' => 'Nimtech', 'currency_code' => 'KES', 'currency_symbol' => 'KSh', 'active_theme' => 'nimtech']);
        Setting::clearCache();
    }

    public function test_phone_price_page_uses_live_inventory_and_item_list_schema(): void
    {
        $phones = Category::create(['name' => 'Phones', 'slug' => 'phones']);
        $apple = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        Product::create(['category_id' => $phones->id, 'brand_id' => $apple->id, 'name' => 'iPhone 17 Pro', 'slug' => 'iphone-17-pro', 'price' => 150000, 'stock' => 2]);

        $this->get('/phone-prices-in-kenya')
            ->assertOk()
            ->assertSee('Phone Prices in Kenya')
            ->assertSee('iPhone 17 Pro')
            ->assertSee('"@type":"ItemList"', false)
            ->assertSee('<meta name="robots" content="index, follow">', false);
    }

    public function test_budget_page_rejects_uncurated_amounts_and_noindexes_pagination(): void
    {
        $this->get('/phones-under-12345-in-kenya')->assertNotFound();
        $this->get('/phones-under-20000-in-kenya?page=2')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_product_schema_has_condition_without_fake_aggregate_rating(): void
    {
        $phones = Category::create(['name' => 'Phones', 'slug' => 'phones']);
        $product = Product::create(['category_id' => $phones->id, 'name' => 'Refurbished iPhone 13', 'slug' => 'refurbished-iphone-13', 'price' => 55000, 'stock' => 1]);

        $this->get(route('products.show', ['productSlug' => $product->slug]))
            ->assertOk()
            ->assertSee('https://schema.org/RefurbishedCondition', false)
            ->assertDontSee('aggregateRating', false)
            ->assertSee('(Rated)');
    }

    public function test_sitemap_contains_curated_price_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/phone-prices-in-kenya', false)
            ->assertSee('/laptops-under-50000-in-kenya', false)
            ->assertSee('/recently-updated-prices', false);
    }
}
