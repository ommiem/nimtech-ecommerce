<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'contact_email', 'contact_phone', 'contact_address',
        'currency_code', 'currency_symbol', 'currency_position',
        'logo_path', 'favicon_path',
        'theme_color',
        'active_theme',
        'deals_under_threshold', 'homepage_cta_heading', 'homepage_cta_subtext',
        'enable_mpesa', 'enable_cod',
        'shipping_flat_rate', 'tax_rate',
        'header_notice_text', 'header_special_tiles',
        'ga4_measurement_id', 'google_ads_id', 'meta_pixel_id', 'tiktok_pixel_id',
        'custom_head_scripts', 'custom_body_scripts',
    ];

    public static function getCached(): ?self
    {
        return Cache::remember('settings.single', 3600, function () {
            return self::query()->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('settings.single');
    }
}
