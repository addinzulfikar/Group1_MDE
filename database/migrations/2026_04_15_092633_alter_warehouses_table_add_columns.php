<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('warehouse_code')->unique()->after('id');
            $table->string('warehouse_name')->after('warehouse_code');
            $table->string('location')->after('warehouse_name');
            $table->integer('capacity')->after('location');
            $table->integer('current_load')->default(0)->after('capacity');
            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->after('current_load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn([
                'warehouse_code',
                'warehouse_name',
                'location',
                'capacity',
                'current_load',
                'status'
            ]);
        });
    }
};