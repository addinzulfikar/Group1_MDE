<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\Package;
use Illuminate\Database\Seeder;

class Module1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Warehouses (10 warehouses)
        $warehouses = [
            [
                'warehouse_name' => 'Jakarta Central Hub',
                'location' => 'Jakarta Pusat',
                'capacity' => 10000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Surabaya Distribution',
                'location' => 'Surabaya, Jawa Timur',
                'capacity' => 8000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Bandung Depot',
                'location' => 'Bandung, Jawa Barat',
                'capacity' => 6000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Medan Logistics',
                'location' => 'Medan, Sumatera Utara',
                'capacity' => 5000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Makassar Port',
                'location' => 'Makassar, Sulawesi Selatan',
                'capacity' => 7000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Palembang Hub',
                'location' => 'Palembang, Sumatera Selatan',
                'capacity' => 4500,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Semarang Center',
                'location' => 'Semarang, Jawa Tengah',
                'capacity' => 5500,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Yogyakarta Station',
                'location' => 'Yogyakarta, DIY',
                'capacity' => 3500,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Bali Gateway',
                'location' => 'Denpasar, Bali',
                'capacity' => 4000,
                'status' => 'available'
            ],
            [
                'warehouse_name' => 'Pontianak Node',
                'location' => 'Pontianak, Kalimantan Barat',
                'capacity' => 3000,
                'status' => 'available'
            ]
        ];

        $createdWarehouses = [];
        foreach ($warehouses as $warehouseData) {
            $createdWarehouses[] = Warehouse::create([
                'warehouse_name' => $warehouseData['warehouse_name'],
                'location'       => $warehouseData['location'],
                'capacity'       => $warehouseData['capacity'],
                'current_load'   => 0,
                'status'         => $warehouseData['status'],
            ]);
        }

        // Create Packages (30 packages)
        $senders = ['PT ABC Indonesia', 'Tokopedia', 'Shopee', 'Lazada', 'Blibli', 'JD.id', 'Bukalapak', 'Klikpak', 'LogistikCorp'];
        $receivers = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Brown', 'Charlie Wilson', 'Diana Prince', 'Edward Norton', 'Fiona Green', 'George Brown'];
        $origins = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Makassar', 'Palembang', 'Semarang', 'Yogyakarta', 'Bali', 'Pontianak'];
        $destinations = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Makassar', 'Palembang', 'Semarang', 'Yogyakarta', 'Bali', 'Pontianak'];
        $statuses = ['registered', 'in_transit', 'delivered', 'pending', 'cancelled'];

        $packages = [];
        for ($i = 1; $i <= 30; $i++) {
            $length = rand(5, 50);
            $width = rand(5, 40);
            $height = rand(5, 30);
            $volume = $length * $width * $height;

            $packages[] = [
                'tracking_number' => 'PKG-2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'sender_name' => $senders[array_rand($senders)],
                'receiver_name' => $receivers[array_rand($receivers)],
                'origin' => $origins[array_rand($origins)],
                'destination' => $destinations[array_rand($destinations)],
                'weight' => rand(1, 50) + rand(0, 9) / 10,
                'length' => $length,
                'width' => $width,
                'height' => $height,
                'volume' => $volume,
                'warehouse_id' => $createdWarehouses[array_rand($createdWarehouses)]->id,
                'package_status' => $statuses[array_rand($statuses)]
            ];

            Package::create($packages[$i - 1]);
        }

        echo "✅ Module 1 seeder completed!\n";
        echo "📦 Created 10 warehouses\n";
        echo "📫 Created 30 packages\n";
        echo "Total packages per dimension:\n";
        echo "  - Small (≤1000 cm³): " . Package::where('volume', '<=', 1000)->count() . "\n";
        echo "  - Medium (1000-5000 cm³): " . Package::whereBetween('volume', [1001, 5000])->count() . "\n";
        echo "  - Large (>5000 cm³): " . Package::where('volume', '>', 5000)->count() . "\n";
    }
}
