<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Shipment Seeder (25,000 records) - Optimized Batch Insert...');

        // Disable foreign key checks for faster insertion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get hub IDs and package IDs (M1) and customer IDs (M3)
        $hubIds = DB::table('hubs')->pluck('id')->toArray();
        $packageIds = DB::table('packages')->pluck('id')->toArray();
        $customerIds = DB::table('users')->pluck('id')->toArray();
        
        // Create test users if none exist
        if (empty($customerIds)) {
            $this->command->info('No customers found, creating test users...');
            $hashedPassword = \Hash::make('password');
            $usersBatch = [];
            for ($i = 0; $i < 100; $i++) {
                $usersBatch[] = [
                    'name' => "Test User $i",
                    'email' => "test$i@example.com",
                    'password' => $hashedPassword,
                    'phone' => '081234567' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'address' => "Jl. Test Street $i",
                    'is_customer' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('users')->insert($usersBatch);
            $customerIds = DB::table('users')->pluck('id')->toArray();
            $this->command->info('Created ' . count($customerIds) . ' test users');
        }
        
        if (empty($hubIds) || empty($packageIds) || empty($customerIds)) {
            $this->command->error('Missing required data: Hubs, Packages, or Customers. Run their seeders first.');
            return;
        }

        $faker = Factory::create('id_ID');
        $batchSize = 500;
        $shipmentBatch = [];

        $this->command->info('Creating 25,000 shipments...');
        
        for ($i = 1; $i <= 25000; $i++) {
            $originId = $hubIds[array_rand($hubIds)];
            
            // Ensure destination is different from origin
            do {
                $destinationId = $hubIds[array_rand($hubIds)];
            } while ($destinationId === $originId);

            $sentAt = $faker->dateTimeBetween('-30 days', 'now');
            $deliveredAt = fake()->randomElement([null, $faker->dateTimeBetween($sentAt, 'now')]);

            $status = fake()->randomElement(['pending', 'in_transit', 'in_hub', 'on_delivery', 'delivered', 'failed']);
            
            // Generate unique tracking number using index + random to guarantee uniqueness
            $trackingNumber = 'TRK' . str_pad($i, 8, '0', STR_PAD_LEFT) . strtoupper(substr(md5(uniqid($i, true)), 0, 4));

            $shipmentBatch[] = [
                'tracking_number' => $trackingNumber,
                'customer_id' => $customerIds[array_rand($customerIds)],      // M3: required
                'package_id' => $packageIds[array_rand($packageIds)],          // M1: required (Option B)
                // Data (sender_name, receiver_name, weight, dimensions) fetched from package relationship
                'origin_hub_id' => $originId,
                'destination_hub_id' => $destinationId,
                'current_hub_id' => $originId,
                'fleet_id' => DB::table('fleets')->inRandomOrder()->first()?->id ?? null,  // M4: optional
                'status' => $status,
                'sent_at' => $sentAt,
                'delivered_at' => $deliveredAt,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert in batches
            if ($i % $batchSize === 0) {
                DB::table('shipments')->insert($shipmentBatch);
                $this->command->info("  ✓ Created $i shipments...");
                $shipmentBatch = [];
            }
        }

        // Insert remaining
        if (!empty($shipmentBatch)) {
            DB::table('shipments')->insert($shipmentBatch);
        }

        $this->command->info('✓ All 25,000 shipments created!');

        // Create tracking histories for each shipment
        $this->command->info('Creating tracking histories (3-5 per shipment)...');
        
        $shipments = DB::table('shipments')->get();
        $historyBatch = [];

        foreach ($shipments as $index => $shipment) {
            $historyCount = rand(3, 5);
            $baseTime = new \DateTime($shipment->created_at);

            for ($i = 0; $i < $historyCount; $i++) {
                $status = $this->getStatusProgression($i, $historyCount, $shipment->status);
                $notes = $this->getNotes($i, $historyCount);

                $historyBatch[] = [
                    'shipment_id' => (int)$shipment->id,
                    'from_hub_id' => (int)$shipment->origin_hub_id,
                    'to_hub_id' => (int)$shipment->destination_hub_id,
                    'status' => $status,
                    'notes' => $notes,
                    'recorded_at' => $baseTime->modify("+$i hours")->format('Y-m-d H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert histories in batches
            if (($index + 1) % 100 === 0 || $index === count($shipments) - 1) {
                DB::table('tracking_histories')->insert($historyBatch);
                $this->command->info('  ✓ Processed ' . ($index + 1) . ' shipment histories...');
                $historyBatch = [];
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✓ Seeding completed successfully!');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('✅ Total: 25,000 shipments + ~100,000 history records');
        $this->command->info('═══════════════════════════════════════════════');
    }

    protected function getStatusProgression($step, $totalSteps, $finalStatus)
    {
        $statuses = ['pending', 'in_transit', 'arrived', 'out_for_delivery', 'delivered'];

        if ($finalStatus === 'failed') {
            $statuses = ['pending', 'in_transit', 'failed'];
        }

        return $statuses[min($step, count($statuses) - 1)];
    }

    protected function getNotes($step, $totalSteps)
    {
        $notes = [
            'Paket terdaftar dan menunggu pengiriman',
            'Paket dalam perjalanan ke hub transit',
            'Paket tiba di hub transit',
            'Paket sedang dalam pengiriman ke alamat tujuan',
            'Paket telah diterima'
        ];

        return $notes[min($step, count($notes) - 1)];
    }
}

