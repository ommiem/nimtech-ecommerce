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
                'contact_address' => 'Rural Urban Credit Finance House, Shop D2, Opposite National Archives, Nairobi CBD, Kenya',
                'currency_code' => 'KES',
                'currency_symbol' => 'KES',
                'currency_position' => 'left',
                'theme_color' => '#E5252A',
                'active_theme' => 'nimtech',
            ]);
        }
    }
}
