<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingScriptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_scripts_render_when_marketing_ids_are_configured(): void
    {
        Setting::create([
            'site_name' => 'Nimtech',
            'active_theme' => 'nimtech',
            'ga4_measurement_id' => 'G-TEST12345',
            'google_ads_id' => 'AW-987654321',
            'meta_pixel_id' => '123456789012345',
            'tiktok_pixel_id' => 'C123ABC456DEF',
            'custom_head_scripts' => '<script>window.__marketingHead = true;</script>',
            'custom_body_scripts' => '<div id="marketing-body-hook">ready</div>',
        ]);

        Setting::clearCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST12345', false)
            ->assertSee("gtag('config', 'G-TEST12345');", false)
            ->assertSee("gtag('config', 'AW-987654321');", false)
            ->assertSee("fbq('init', '123456789012345');", false)
            ->assertSee("ttq.load('C123ABC456DEF');", false)
            ->assertSee('window.__marketingHead = true;', false)
            ->assertSee('marketing-body-hook', false);
    }
}
