<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Add Foreign Key dependencies to Shipment table
     * - Link to M1 (Packages)
     * - Link to M3 (Users/Customers)
     * - Link to M4 (Fleets & Hubs)
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // ── M3 Dependency: Customer ownership (for auth & privacy)
            // CASCADE delete ensures no orphaned shipments when customer is deleted
            $table->foreignId('customer_id')
                ->nullable()
                ->after('tracking_number')
                ->constrained('users')
                ->cascadeOnDelete();
            
            // ── M1 Dependency: Package reference (eliminate data duplication)
            $table->foreignId('package_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('packages')
                ->cascadeOnDelete();
            
            // ── M4 Dependency: Fleet assignment (for pickup & delivery)
            $table->foreignId('fleet_id')
                ->nullable()
                ->after('destination_hub_id')
                ->constrained('fleets')
                ->cascadeOnDelete();
            
            // ── M4 Dependency: Current hub location (for realtime tracking)
            $table->foreignId('current_hub_id')
                ->nullable()
                ->after('fleet_id')
                ->constrained('hubs')
                ->cascadeOnDelete();
            
            // Add indices for common queries
            $table->index('customer_id');
            $table->index(['customer_id', 'status']);
            $table->index('package_id');
            $table->index('fleet_id');
            $table->index('current_hub_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Drop foreign keys by column name, not by constraint name
            try {
                $table->dropForeign(['customer_id']);
            } catch (\Exception $e) {
                \Log::warning('FK customer_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropForeign(['package_id']);
            } catch (\Exception $e) {
                \Log::warning('FK package_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropForeign(['fleet_id']);
            } catch (\Exception $e) {
                \Log::warning('FK fleet_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropForeign(['current_hub_id']);
            } catch (\Exception $e) {
                \Log::warning('FK current_hub_id not found: ' . $e->getMessage());
            }
            
            // Drop indices
            try {
                $table->dropIndex('shipments_customer_id_index');
            } catch (\Exception $e) {
                \Log::warning('Index customer_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropIndex('shipments_customer_id_status_index');
            } catch (\Exception $e) {
                \Log::warning('Index customer_id_status not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropIndex('shipments_package_id_index');
            } catch (\Exception $e) {
                \Log::warning('Index package_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropIndex('shipments_fleet_id_index');
            } catch (\Exception $e) {
                \Log::warning('Index fleet_id not found: ' . $e->getMessage());
            }
            
            try {
                $table->dropIndex('shipments_current_hub_id_index');
            } catch (\Exception $e) {
                \Log::warning('Index current_hub_id not found: ' . $e->getMessage());
            }
            
            // Drop columns
            try {
                $table->dropColumn(['customer_id', 'package_id', 'fleet_id', 'current_hub_id']);
            } catch (\Exception $e) {
                \Log::warning('Columns not dropped: ' . $e->getMessage());
            }
        });
    }
};
