<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
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
        $this->command->info('✅ 50 Hubs created successfully!');
    }
}
