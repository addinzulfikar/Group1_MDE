<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Disable mass assignment checks and clear old data for fresh re-seed
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('tracking_histories')->truncate();
        \Illuminate\Support\Facades\DB::table('shipments')->truncate();
        \Illuminate\Support\Facades\DB::table('fleet_logs')->truncate();
        \Illuminate\Support\Facades\DB::table('fleets')->truncate();
        \Illuminate\Support\Facades\DB::table('hubs')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Creating 50 Hubs...');
        $hubs = [];
        $now = now();
        for ($i = 1; $i <= 50; $i++) {
            $hubs[] = [
                'name' => $faker->city . ' Hub',
                'capacity' => $faker->numberBetween(5000, 20000),
                'current_load' => $faker->numberBetween(500, 4000),
                'status' => $faker->randomElement(['available', 'available', 'full', 'overload']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        \Illuminate\Support\Facades\DB::table('hubs')->insert($hubs);
        $hubIds = \Illuminate\Support\Facades\DB::table('hubs')->pluck('id')->toArray();

        $this->command->info('Creating 500 Fleets...');
        $fleets = [];
        for ($i = 1; $i <= 500; $i++) {
            $fleets[] = [
                'plate_number' => strtoupper($faker->bothify('?? #### ??')),
                'type' => $faker->randomElement(['motorcycle', 'van', 'truck', 'truck']),
                'capacity' => $faker->numberBetween(100, 5000),
                'status' => $faker->randomElement(['idle', 'in_transit', 'maintenance']),
                'current_hub_id' => $faker->randomElement($hubIds),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        \Illuminate\Support\Facades\DB::table('fleets')->insert($fleets);
        $fleetIds = \Illuminate\Support\Facades\DB::table('fleets')->pluck('id')->toArray();

        $this->command->info('Creating 5,000 Fleet Logs...');
        $fleetLogs = [];
        $chunkSize = 1000;

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

        $this->command->info('✅ Seeding Modul 4 (5,000 Fleet Logs) Completed Successfully!');

        // Call ShipmentSeeder for Modul 2
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('Starting Modul 2: Tracking System Seeder');
        $this->command->info('═══════════════════════════════════════════════');
        $this->call(ShipmentSeeder::class);
    }
}
