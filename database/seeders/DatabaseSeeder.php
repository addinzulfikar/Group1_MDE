<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users FIRST (needed by ShipmentSeeder)
        User::factory(100)->create();
        
        // Create primary test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1, Jakarta',
            'is_customer' => true,
        ]);

        // Run all seeders
        $this->call([
            HubSeeder::class,
            FleetSeeder::class,
            FleetLogSeeder::class,   // 5.000+ log armada (ketentuan teknis UTS)
            Module1Seeder::class,
            ShipmentSeeder::class,  // Modul 2 Tracking System
        ]);
    }
}
