<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_category_slug_redirects_to_canonical_slug(): void
    {
        $category = Category::create([
            'name' => 'Laptop Chargers',
            'slug' => 'Laptop Chargers',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'HP 65W Adapter',
            'slug' => 'hp-65w-adapter',
            'price' => 2500,
            'stock' => 5,
        ]);

        $this->get('/category/Laptop%20Chargers')
            ->assertRedirect(route('categories.show', 'laptop-chargers'));

        $this->get(route('categories.show', 'laptop-chargers'))
            ->assertOk()
            ->assertSee('Buy Laptop Chargers in Kenya', false)
            ->assertSee('Buy Laptop Chargers in Kenya at Laravel. Compare prices, trusted brands, warranty support, and fast delivery in Nairobi and across Kenya.', false)
            ->assertSee('<meta property="og:url" content="https://localhost/category/laptop-chargers">', false)
            ->assertSee('<link rel="canonical" href="https://localhost/category/laptop-chargers">', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('Laptop Chargers')
            ->assertSee('HP 65W Adapter');

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('https://localhost/category/laptop-chargers', false)
            ->assertSee('https://localhost/products/hp-65w-adapter', false);
    }

    public function test_sitemap_uses_canonical_category_slug(): void
    {
        $category = Category::create([
            'name' => 'TV Boxes',
            'slug' => 'tV Boxes',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Android TV Box',
            'slug' => 'android-tv-box',
            'price' => 3500,
            'stock' => 2,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertSee(route('categories.show', 'tv-boxes'), false);
        $response->assertDontSee('tV%20Boxes', false);
        $response->assertDontSee('TV%20Boxes', false);
    }

    public function test_empty_category_is_noindexed_and_omitted_from_sitemap(): void
    {
        $category = Category::create([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
        ]);

        $this->get(route('categories.show', $category->canonical_slug))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee(route('categories.show', $category->canonical_slug), false);
    }
}
