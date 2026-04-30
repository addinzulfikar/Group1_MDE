<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * PURPOSE: Normalize M2 Database (Option B - Full Integration)
     * 
     * REMOVES:
     * - Duplicate data from Package (sender_name, receiver_name, weight, dimensions)
     * - Data redundancy
     * 
     * ENFORCES:
     * - M2 MUST have customer_id (from M3)
     * - M2 can have package_id (from M1) - nullable for manual shipments
     * - M2 tracks via origin/destination/current hubs (from M4)
     * 
     * PHILOSOPHY: M2 is CORE TRACKING, not data storage
     * Data fetched via relationships, not duplicated
     */
    public function up(): void
    {
        // This migration normalizes M2 database by:
        // 1. Removing duplicate columns (they'll be fetched via package relationship)
        // 2. Ensuring customer_id is NOT NULL (M2 must be owned by someone)
        
        // Step 1: Remove duplicate columns if they exist
        Schema::table('shipments', function (Blueprint $table) {
            $columns = ['sender_name', 'sender_phone', 'sender_address', 'receiver_name', 'receiver_phone', 'receiver_address', 'weight', 'length', 'width', 'height'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('shipments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Step 2: Make customer_id NOT NULL
        // Since FK is CASCADE, this should work
        if (Schema::hasColumn('shipments', 'customer_id')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->notNullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Restore duplicate columns only if rolling back
            $table->string('sender_name')->nullable();
            $table->string('sender_phone')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->text('receiver_address')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
        });

        // Restore nullable customer_id with SET NULL
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->bigUnsignedInteger('customer_id')->nullable()->change();
            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
