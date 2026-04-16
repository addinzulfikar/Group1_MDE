<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FleetLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $this->command->info('Creating 5,000 Fleet Logs...');
        
        $hubIds = \Illuminate\Support\Facades\DB::table('hubs')->pluck('id')->toArray();
        $fleetIds = \Illuminate\Support\Facades\DB::table('fleets')->pluck('id')->toArray();
        
        $fleetLogs = [];
        $chunkSize = 1000;
        $now = now();

        for ($i = 1; $i <= 5000; $i++) {
            $origin = $faker->randomElement($hubIds);
            
            // Generate distinct origin and destination
            do {
                $destination = $faker->randomElement($hubIds);
            } while ($destination === $origin);
            
            $departedAt = \Illuminate\Support\Carbon::now()->subDays(rand(1, 30))->subMinutes(rand(1, 1440));
            $arrivedAt = (clone $departedAt)->addHours(rand(1, 48));

            $fleetLogs[] = [
                'fleet_id' => $faker->randomElement($fleetIds),
                'origin_hub_id' => $origin,
                'destination_hub_id' => $destination,
                'status' => $faker->randomElement(['departed', 'arrived', 'delayed']),
                'departed_at' => $departedAt,
                'arrived_at' => $arrivedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($i % $chunkSize === 0) {
                \Illuminate\Support\Facades\DB::table('fleet_logs')->insert($fleetLogs);
                $fleetLogs = []; 
            }
        }
        
        if (count($fleetLogs) > 0) {
            \Illuminate\Support\Facades\DB::table('fleet_logs')->insert($fleetLogs);
        }
        
        $this->command->info('✅ 5,000 Fleet Logs created successfully!');
    }
}
