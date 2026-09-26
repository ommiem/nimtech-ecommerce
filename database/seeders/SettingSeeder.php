<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        if (! Setting::query()->exists()) {
            Setting::create([
                'site_name' => 'Nimtech',
                'contact_email' => 'support@nimtech.co.ke',
                'contact_phone' => '+254 711 948 136',
                'contact_address' => 'Nairobi, Kenya',
                'currency_code' => 'KES',
                'currency_symbol' => 'KES',
                'currency_position' => 'left',
                'theme_color' => '#ef2f2f',
                'active_theme' => 'nimtech',
            ]);
        }
    }
}
