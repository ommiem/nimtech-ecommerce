<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        Promotion::updateOrCreate(['code' => 'WELCOME10'], [
            'type' => 'percent',
            'value' => 10,
            'usage_limit' => 1000,
            'active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(6),
        ]);

        Promotion::updateOrCreate(['code' => 'OFF100'], [
            'type' => 'amount',
            'value' => 100,
            'usage_limit' => 500,
            'active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(3),
        ]);
    }
}

