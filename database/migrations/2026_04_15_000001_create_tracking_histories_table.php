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
        Schema::create('tracking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('from_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->foreignId('to_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            
            // Status: pending, in_transit, arrived, out_for_delivery, delivered, failed
            $table->enum('status', ['pending', 'in_transit', 'arrived', 'out_for_delivery', 'delivered', 'failed'])->index();
            
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->useCurrent()->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_histories');
    }
};
