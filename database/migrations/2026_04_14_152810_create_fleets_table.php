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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->enum('type', ['motorcycle', 'van', 'truck']);
            $table->integer('capacity');
            $table->enum('status', ['idle', 'in_transit', 'maintenance'])->default('idle');
            $table->foreignId('current_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleets');
    }
};
