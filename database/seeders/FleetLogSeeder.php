<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FleetLogSeeder extends Seeder
{
    /**
     * Generate 5.000+ log armada (fleet_logs) sesuai ketentuan teknis UTS.
     * Setiap fleet mendapat riwayat perjalanan antar hub yang realistis.
     */
    public function run(): void
    {
        $this->command->info('Starting FleetLog Seeder (5.000+ records)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $fleetIds = DB::table('fleets')->pluck('id')->toArray();
        $hubIds   = DB::table('hubs')->pluck('id')->toArray();

        if (empty($fleetIds)) {
            $this->command->error('No fleets found! Run FleetSeeder first.');
            return;
        }
        if (count($hubIds) < 2) {
            $this->command->error('Need at least 2 hubs! Run HubSeeder first.');
            return;
        }

        $statuses  = ['departed', 'arrived', 'delayed'];
        $batchSize = 500;
        $batch     = [];
        $total     = 5000;
        $now       = now();

        for ($i = 1; $i <= $total; $i++) {
            $fleetId  = $fleetIds[array_rand($fleetIds)];
            $originId = $hubIds[array_rand($hubIds)];

            // Pastikan destination berbeda dari origin
            do {
                $destId = $hubIds[array_rand($hubIds)];
            } while ($destId === $originId);

            // Waktu keberangkatan acak dalam 60 hari terakhir
            $departedAt = $now->copy()->subDays(rand(1, 60))->subHours(rand(1, 23));

            // Status menentukan apakah sudah tiba atau belum
            $status    = $statuses[array_rand($statuses)];
            $arrivedAt = null;

            if ($status === 'arrived') {
                // Durasi perjalanan antara 1–36 jam (realistis untuk logistik nasional)
                $arrivedAt = $departedAt->copy()->addHours(rand(1, 36));
            } elseif ($status === 'delayed') {
                // Delayed: berangkat tapi tiba jauh lebih lama
                $arrivedAt = $departedAt->copy()->addHours(rand(24, 72));
            }
            // 'departed' → belum tiba, arrived_at tetap null

            $batch[] = [
                'fleet_id'           => $fleetId,
                'origin_hub_id'      => $originId,
                'destination_hub_id' => $destId,
                'status'             => $status,
                'departed_at'        => $departedAt->format('Y-m-d H:i:s'),
                'arrived_at'         => $arrivedAt ? $arrivedAt->format('Y-m-d H:i:s') : null,
                'created_at'         => $departedAt->format('Y-m-d H:i:s'),
                'updated_at'         => $arrivedAt
                                            ? $arrivedAt->format('Y-m-d H:i:s')
                                            : $departedAt->format('Y-m-d H:i:s'),
            ];

            if ($i % $batchSize === 0) {
                DB::table('fleet_logs')->insert($batch);
                $this->command->info("  ✓ Created {$i} fleet logs...");
                $batch = [];
            }
        }

        // Insert sisa batch
        if (!empty($batch)) {
            DB::table('fleet_logs')->insert($batch);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $totalCreated = DB::table('fleet_logs')->count();
        $this->command->info("✓ FleetLog Seeder selesai! Total: {$totalCreated} log armada.");
    }
}
