<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('ga4_measurement_id')->nullable()->after('header_special_tiles');
            $table->string('google_ads_id')->nullable()->after('ga4_measurement_id');
            $table->string('meta_pixel_id')->nullable()->after('google_ads_id');
            $table->string('tiktok_pixel_id')->nullable()->after('meta_pixel_id');
            $table->longText('custom_head_scripts')->nullable()->after('tiktok_pixel_id');
            $table->longText('custom_body_scripts')->nullable()->after('custom_head_scripts');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'ga4_measurement_id',
                'google_ads_id',
                'meta_pixel_id',
                'tiktok_pixel_id',
                'custom_head_scripts',
                'custom_body_scripts',
            ]);
        });
    }
};
