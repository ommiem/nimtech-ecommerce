<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_and_category_pages_render_h1_and_richer_meta_description(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        Setting::clearCache();

        $category = Category::create([
            'name' => 'Thermal Roll Paper',
            'slug' => 'thermal-roll-paper',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Thermal Receipt Roll 80x80',
            'slug' => 'thermal-receipt-roll-80x80',
            'price' => 250,
            'stock' => 50,
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('<h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">Phones, Laptops and Electronics in Kenya</h1>', false)
            ->assertSee('Compare prices, verified stock, warranty support, and fast delivery on in-stock products.', false);

        $this->get(route('categories.show', $category->canonical_slug))
            ->assertOk()
            ->assertSee('<h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">Buy Thermal Roll Paper in Kenya</h1>', false)
            ->assertSee('Compare Thermal Roll Paper prices, verified stock, and warranty support from Nimtech with delivery in Nairobi and across Kenya.', false);
    }

    public function test_filtered_catalog_urls_are_noindexed_and_canonicalized_to_clean_pages(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        Setting::clearCache();

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'HP EliteBook 840',
            'slug' => 'hp-elitebook-840',
            'price' => 55000,
            'stock' => 5,
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<link rel="canonical" href="https://localhost/products">', false);

        $this->get(route('products.index', ['sort' => 'price_asc', 'price_max' => 5000]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="https://localhost/products">', false);

        $this->get(route('categories.show', ['category' => $category->canonical_slug, 'page' => 2]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="https://localhost/category/laptops">', false);
    }
}
