<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_campaign_page_renders_bundle_and_compare_content(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'HP EliteBook 840 G8',
            'slug' => 'hp-elitebook-840-g8',
            'description' => "- 14 inch display\n- 16GB RAM\n- 512GB SSD",
            'price' => 65000,
            'stock' => 8,
        ]);

        $promotion = Promotion::create([
            'code' => 'HPLAPTOPWEEK',
            'type' => 'percent',
            'value' => 10,
            'active' => true,
        ]);

        $campaign = Campaign::create([
            'title' => 'HP Laptop Week',
            'slug' => 'hp-laptop-week',
            'summary' => 'Curated HP work and school laptops ready for quick order.',
            'meta_title' => 'HP Laptop Week in Kenya | Nimtech',
            'meta_description' => 'Compare curated HP laptops in Kenya and add the full bundle to cart in one tap.',
            'whatsapp_message' => 'Hello, I would like the HP Laptop Week bundle.',
            'promotion_id' => $promotion->id,
            'compare_enabled' => true,
            'published' => true,
        ]);

        $campaign->products()->attach($product->id, [
            'quantity' => 2,
            'sort_order' => 1,
        ]);

        $this->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('HP Laptop Week')
            ->assertSee('Products in this campaign')
            ->assertSee('Quick compare')
            ->assertSee('Bundle quantity: <strong>2</strong>', false)
            ->assertSee('Promo code: <strong>HPLAPTOPWEEK</strong>', false)
            ->assertSee('HP EliteBook 840 G8');
    }

    public function test_campaign_bundle_add_route_adds_all_configured_products_to_cart(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        $category = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);

        $mouse = Product::create([
            'category_id' => $category->id,
            'name' => 'Wireless Mouse',
            'slug' => 'wireless-mouse',
            'price' => 1200,
            'stock' => 10,
        ]);

        $keyboard = Product::create([
            'category_id' => $category->id,
            'name' => 'USB Keyboard',
            'slug' => 'usb-keyboard',
            'price' => 1800,
            'stock' => 10,
        ]);

        $campaign = Campaign::create([
            'title' => 'Accessories Under 2K',
            'slug' => 'accessories-under-2k',
            'published' => true,
        ]);

        $campaign->products()->attach($mouse->id, [
            'quantity' => 2,
            'sort_order' => 1,
        ]);
        $campaign->products()->attach($keyboard->id, [
            'quantity' => 1,
            'sort_order' => 2,
        ]);

        $this->from(route('campaigns.show', $campaign))
            ->post(route('campaigns.bundle.add', $campaign))
            ->assertRedirect(route('campaigns.show', $campaign))
            ->assertSessionHas('success', 'Bundle items added to cart.');

        $this->assertSame([
            $mouse->id => 2,
            $keyboard->id => 1,
        ], session('cart.items'));
    }

    public function test_published_campaigns_are_included_in_sitemap(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        $published = Campaign::create([
            'title' => 'Smart TV Deals Kenya',
            'slug' => 'smart-tv-deals-kenya',
            'published' => true,
        ]);

        $draft = Campaign::create([
            'title' => 'Draft Campaign',
            'slug' => 'draft-campaign',
            'published' => false,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('campaigns.show', $published), false)
            ->assertDontSee(route('campaigns.show', $draft), false);
    }

    public function test_homepage_surfaces_published_campaigns_only(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
        ]);

        Campaign::create([
            'title' => 'HP Laptop Week',
            'slug' => 'hp-laptop-week',
            'summary' => 'Curated HP laptops for school and office work.',
            'published' => true,
        ]);

        Campaign::create([
            'title' => 'Hidden Draft Campaign',
            'slug' => 'hidden-draft-campaign',
            'published' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Featured campaigns')
            ->assertSee('HP Laptop Week')
            ->assertDontSee('Hidden Draft Campaign');
    }
}
