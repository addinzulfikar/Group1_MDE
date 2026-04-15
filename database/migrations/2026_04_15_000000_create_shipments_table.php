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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique()->index();
            
            // Sender info
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->text('sender_address');
            
            // Receiver info
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->text('receiver_address');
            
            // Dimensions & weight
            $table->decimal('weight', 8, 2); // kg
            $table->decimal('length', 8, 2); // cm
            $table->decimal('width', 8, 2);  // cm
            $table->decimal('height', 8, 2); // cm
            
            // Hubs
            $table->foreignId('origin_hub_id')->constrained('hubs')->cascadeOnDelete();
            $table->foreignId('destination_hub_id')->constrained('hubs')->cascadeOnDelete();
            
            // Status: pending, in_transit, in_hub, on_delivery, delivered, failed
            $table->enum('status', ['pending', 'in_transit', 'in_hub', 'on_delivery', 'delivered', 'failed'])->default('pending')->index();
            
            // Shipment timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
