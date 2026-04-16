<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks and clear old data for fresh re-seed
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('tracking_histories')->truncate();
        \Illuminate\Support\Facades\DB::table('shipments')->truncate();
        \Illuminate\Support\Facades\DB::table('fleet_logs')->truncate();
        \Illuminate\Support\Facades\DB::table('fleets')->truncate();
        \Illuminate\Support\Facades\DB::table('hubs')->truncate();
        \Illuminate\Support\Facades\DB::table('packages')->truncate();
        \Illuminate\Support\Facades\DB::table('warehouses')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('Starting Database Seeding...');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('');

        // Module 4 Seeding: Infrastructure (Hubs, Fleets, Fleet Logs)
        $this->command->info('📦 Module 4: Infrastructure Seeding');
        $this->call(HubSeeder::class);
        $this->call(FleetSeeder::class);
        $this->call(FleetLogSeeder::class);
        $this->command->info('✅ Module 4 Completed!');

        $this->command->info('');

        // Module 1 Seeding: Warehouse & Package
        $this->command->info('📦 Module 1: Warehouse & Package Seeding');
        $this->call(WarehouseSeeder::class);
        $this->call(PackageSeeder::class);
        $this->command->info('✅ Module 1 Completed!');

        $this->command->info('');

        // Module 2 Seeding: Shipment & Tracking
        $this->command->info('📦 Module 2: Shipment & Tracking Seeding');
        $this->call(ShipmentSeeder::class);
        $this->command->info('✅ Module 2 Completed!');

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('✅ All Seeding Completed Successfully!');
        $this->command->info('═══════════════════════════════════════════════');
    }
}
