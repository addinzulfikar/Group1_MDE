<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rate_rules', function (Blueprint $table): void {
            $table->id();
            $table->enum('service_type', ['regular', 'express', 'same_day']);
            $table->decimal('min_distance_km', 10, 2);
            $table->decimal('max_distance_km', 10, 2);
            $table->decimal('base_price', 12, 2);
            $table->decimal('price_per_km', 12, 2);
            $table->decimal('price_per_kg', 12, 2);
            $table->decimal('fuel_surcharge_percent', 5, 2)->default(0);
            $table->decimal('fragile_surcharge', 12, 2)->default(0);
            $table->decimal('insurance_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'min_distance_km', 'max_distance_km'], 'idx_rate_rule_service_distance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rate_rules');
    }
};
