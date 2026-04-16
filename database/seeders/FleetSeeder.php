<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FleetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $this->command->info('Creating 500 Fleets...');
        
        $hubIds = \Illuminate\Support\Facades\DB::table('hubs')->pluck('id')->toArray();
        $fleets = [];
        $now = now();
        
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
        $this->command->info('✅ 500 Fleets created successfully!');
    }
}
