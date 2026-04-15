<?php

namespace Database\Seeders;

use App\Models\ShippingRateRule;
use Illuminate\Database\Seeder;

class ShippingRateRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['regular', 0, 50, 7000, 800, 3000, 5, 2000, 0.5],
            ['regular', 50.01, 250, 9000, 700, 2800, 6, 2000, 0.5],
            ['regular', 250.01, 1500, 12000, 600, 2500, 7, 2500, 0.5],
            ['express', 0, 50, 10000, 1200, 4500, 7, 3000, 0.75],
            ['express', 50.01, 250, 12000, 1000, 4000, 8, 3000, 0.75],
            ['express', 250.01, 1500, 15000, 900, 3800, 9, 3500, 0.75],
            ['same_day', 0, 30, 16000, 1800, 6000, 10, 5000, 1],
            ['same_day', 30.01, 80, 22000, 1600, 5500, 10, 5000, 1],
        ];

        foreach ($rules as $rule) {
            ShippingRateRule::query()->updateOrCreate([
                'service_type' => $rule[0],
                'min_distance_km' => $rule[1],
                'max_distance_km' => $rule[2],
            ], [
                'base_price' => $rule[3],
                'price_per_km' => $rule[4],
                'price_per_kg' => $rule[5],
                'fuel_surcharge_percent' => $rule[6],
                'fragile_surcharge' => $rule[7],
                'insurance_percent' => $rule[8],
                'is_active' => true,
            ]);
        }
    }
}
