<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouseBlueprint = [
            ['warehouse_name' => 'Jakarta Central Hub', 'capacity' => 10000, 'current_load' => 6500],
            ['warehouse_name' => 'Surabaya Distribution', 'capacity' => 8000, 'current_load' => 5200],
            ['warehouse_name' => 'Bandung Depot', 'capacity' => 6000, 'current_load' => 3800],
            ['warehouse_name' => 'Medan Logistics', 'capacity' => 5000, 'current_load' => 2500],
            ['warehouse_name' => 'Makassar Port', 'capacity' => 7000, 'current_load' => 4200],
            ['warehouse_name' => 'Palembang Hub', 'capacity' => 4500, 'current_load' => 2700],
            ['warehouse_name' => 'Semarang Center', 'capacity' => 5500, 'current_load' => 3300],
            ['warehouse_name' => 'Yogyakarta Station', 'capacity' => 3500, 'current_load' => 1800],
            ['warehouse_name' => 'Bali Gateway', 'capacity' => 4000, 'current_load' => 2400],
            ['warehouse_name' => 'Pontianak Node', 'capacity' => 3000, 'current_load' => 1500],
        ];

        foreach ($warehouseBlueprint as $item) {
            $percentage = ($item['capacity'] > 0)
                ? ($item['current_load'] / $item['capacity']) * 100
                : 0;

            $status = 'available';
            if ($percentage >= 100) {
                $status = 'overload';
            } elseif ($percentage >= 90) {
                $status = 'full';
            }

            \App\Models\Hub::updateOrCreate(
                ['name' => $item['warehouse_name']],
                [
                    'capacity' => $item['capacity'],
                    'current_load' => $item['current_load'],
                    'status' => $status,
                ]
            );
        }
    }
}
