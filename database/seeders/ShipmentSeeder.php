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

        // Get hub IDs (should be 50 from previous seeder)
        $hubIds = DB::table('hubs')->pluck('id')->toArray();
        
        if (empty($hubIds)) {
            $this->command->error('No hubs found! Please run hub seeding first.');
            return;
        }

        $faker = Factory::create('id_ID');
        $batchSize = 500;
        $shipmentBatch = [];
        $trackingHistoryBatch = [];

        $this->command->info('Creating 25,000 shipments and tracking histories...');
        
        for ($i = 1; $i <= 25000; $i++) {
            $originId = $hubIds[array_rand($hubIds)];
            
            // Ensure destination is different from origin
            do {
                $destinationId = $hubIds[array_rand($hubIds)];
            } while ($destinationId === $originId);

            $sentAt = $faker->dateTimeBetween('-30 days', 'now');
            $deliveredAt = fake()->randomElement([null, $faker->dateTimeBetween($sentAt, 'now')]);

            $status = fake()->randomElement(['pending', 'in_transit', 'in_hub', 'on_delivery', 'delivered', 'failed']);
            
            // Generate unique tracking number
            $trackingNumber = 'TRK' . substr(microtime(true) * 10000, -10) . random_int(1000, 9999);

            $shipmentBatch[] = [
                'tracking_number' => $trackingNumber,
                'sender_name' => $faker->name(),
                'sender_phone' => $faker->phoneNumber(),
                'sender_address' => $faker->address(),
                'receiver_name' => $faker->name(),
                'receiver_phone' => $faker->phoneNumber(),
                'receiver_address' => $faker->address(),
                'weight' => $faker->randomFloat(2, 0.5, 50),
                'length' => $faker->numberBetween(5, 200),
                'width' => $faker->numberBetween(5, 200),
                'height' => $faker->numberBetween(5, 200),
                'origin_hub_id' => $originId,
                'destination_hub_id' => $destinationId,
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

