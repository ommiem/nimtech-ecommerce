<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_outputs_metadata_and_website_schema(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'contact_email' => 'admin@nimtech.co.ke',
            'contact_phone' => '+254700000000',
            'contact_address' => 'Nairobi, Kenya',
            'active_theme' => 'nimtech',
        ]);

        Setting::clearCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Phones, Laptops and Electronics in Kenya | Nimtech</title>', false)
            ->assertSee('Buy phones, laptops, TVs and electronics in Nairobi and across Kenya from Nimtech. Competitive prices, warranty support and fast delivery.', false)
            ->assertSee('<meta property="og:url" content="https://localhost/">', false)
            ->assertSee('<link rel="canonical" href="https://localhost/">', false)
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"WebSite"', false)
            ->assertSee('"@type":"SearchAction"', false)
            ->assertSee('https://localhost/products?q={search_term_string}', false)
            ->assertSee('admin@nimtech.co.ke', false)
            ->assertSee('+254700000000', false);
    }
}
