<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Seed 100 packages distributed across existing warehouses.
     *
     * After inserting all packages, warehouse current_load and status
     * are recalculated automatically via Warehouse::recalculateLoad().
     */
    public function run(): void
    {
        // Fetch warehouse IDs that exist in the database
        $warehouseIds = Warehouse::pluck('id')->toArray();

        if (empty($warehouseIds)) {
            $this->command->warn('No warehouses found. Run WarehouseSeeder first.');
            return;
        }

        $senders = [
            'PT Tokopedia Indonesia', 'Shopee Express', 'Lazada Logistics',
            'JD.id', 'Blibli Commerce', 'Bukalapak', 'PT GoSend Indonesia',
            'Grab Express', 'SiCepat Ekspres', 'JNE Logistics',
            'PT TIKI', 'Anteraja', 'Wahana Express', 'Ninja Xpress',
            'PT Pos Indonesia', 'Lion Parcel', 'RPX Holding',
        ];

        $receivers = [
            'Ahmad Fauzi', 'Siti Rahma', 'Budi Santoso', 'Dewi Lestari',
            'Rizky Pratama', 'Nurul Hidayah', 'Eko Prasetyo', 'Fitri Andriani',
            'Hendra Gunawan', 'Indah Permata', 'Joko Widodo', 'Kartini Sari',
            'Luki Hakim', 'Maya Putri', 'Nanang Suryadi', 'Olivia Tan',
            'Putra Alamsyah', 'Qonita Azzahra', 'Rendra Kusuma', 'Sari Dewi',
        ];

        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Makassar',
            'Palembang', 'Semarang', 'Yogyakarta', 'Denpasar', 'Pontianak',
            'Banjarmasin', 'Balikpapan', 'Samarinda', 'Pekanbaru', 'Padang',
            'Manado', 'Kupang', 'Jayapura', 'Mataram', 'Ambon',
        ];

        $statuses = ['registered', 'registered', 'registered', 'shipped', 'delivered'];

        // Predefined 100 packages with realistic varied dimensions
        // – Small  : volume ≤ 1000 cm³  (target ~30 packages)
        // – Medium : volume 1001–5000    (target ~40 packages)
        // – Large  : volume > 5000       (target ~30 packages)
        $dimensionSets = [
            // Small (30)
            [10, 10, 8],   [12, 8, 10],  [15, 10, 6],  [8, 8, 12],   [10, 10, 10],
            [20, 10, 5],   [12, 12, 6],  [15, 8, 8],   [10, 8, 12],  [18, 10, 5],
            [9, 9, 9],     [14, 10, 7],  [16, 8, 7],   [11, 10, 9],  [13, 9, 8],
            [20, 7, 7],    [10, 10, 9],  [12, 10, 8],  [15, 9, 7],   [8, 8, 15],
            [10, 10, 7],   [12, 8, 9],   [14, 8, 8],   [9, 9, 12],   [11, 9, 9],
            [16, 9, 6],    [10, 10, 8],  [13, 10, 7],  [15, 8, 8],   [11, 11, 8],
            // Medium (40)
            [20, 15, 10],  [25, 20, 8],  [30, 15, 10], [25, 15, 12], [20, 20, 10],
            [35, 15, 8],   [30, 20, 8],  [25, 18, 10], [22, 18, 12], [30, 18, 8],
            [28, 18, 10],  [24, 20, 10], [30, 16, 10], [26, 18, 10], [32, 15, 10],
            [20, 20, 12],  [28, 16, 10], [25, 20, 9],  [30, 14, 10], [22, 20, 10],
            [35, 14, 8],   [28, 20, 8],  [26, 20, 9],  [32, 16, 9],  [24, 18, 11],
            [30, 18, 9],   [28, 18, 9],  [25, 18, 11], [22, 20, 11], [30, 16, 9],
            [26, 16, 10],  [24, 18, 11], [28, 14, 11], [25, 16, 12], [22, 18, 11],
            [30, 14, 11],  [26, 18, 10], [24, 20, 10], [28, 16, 11], [25, 18, 10],
            // Large (30)
            [50, 40, 30],  [60, 40, 25], [50, 45, 30], [55, 40, 30], [60, 45, 25],
            [70, 50, 20],  [50, 50, 30], [60, 50, 25], [55, 45, 28], [65, 40, 28],
            [70, 40, 25],  [50, 50, 25], [60, 45, 28], [55, 50, 25], [65, 45, 25],
            [70, 50, 22],  [50, 45, 35], [60, 50, 22], [55, 45, 30], [65, 50, 22],
            [80, 50, 20],  [50, 40, 35], [60, 40, 30], [55, 40, 32], [65, 40, 30],
            [70, 45, 25],  [50, 50, 28], [60, 50, 26], [55, 50, 28], [65, 45, 28],
        ];

        $packages = [];
        $now = now();

        for ($i = 1; $i <= 100; $i++) {
            $dim    = $dimensionSets[$i - 1];
            $length = $dim[0];
            $width  = $dim[1];
            $height = $dim[2];
            $volume = $length * $width * $height;
            $weight = round(($volume / 5000) * 10 + rand(1, 5) + rand(0, 9) / 10, 2);

            $packages[] = [
                'tracking_number' => 'PKG-2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'sender_name'     => $senders[array_rand($senders)],
                'receiver_name'   => $receivers[array_rand($receivers)],
                'origin'          => $cities[array_rand($cities)],
                'destination'     => $cities[array_rand($cities)],
                'weight'          => $weight,
                'length'          => $length,
                'width'           => $width,
                'height'          => $height,
                'volume'          => $volume,
                'warehouse_id'    => $warehouseIds[($i - 1) % count($warehouseIds)],
                'package_status'  => $statuses[array_rand($statuses)],
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Bulk insert for performance
        DB::table('packages')->insert($packages);

        // Recalculate current_load and status for every warehouse
        $this->command->info('Recalculating warehouse loads...');
        foreach (Warehouse::all() as $warehouse) {
            $warehouse->recalculateLoad();
        }

        $this->command->info('✅ PackageSeeder completed!');
        $this->command->line('📦 100 packages inserted');
        $this->command->line('  - Small  (≤ 1,000 cm³): '  . Package::where('volume', '<=', 1000)->count());
        $this->command->line('  - Medium (1,001–5,000 cm³): ' . Package::whereBetween('volume', [1001, 5000])->count());
        $this->command->line('  - Large  (> 5,000 cm³): '  . Package::where('volume', '>', 5000)->count());
    }
}
