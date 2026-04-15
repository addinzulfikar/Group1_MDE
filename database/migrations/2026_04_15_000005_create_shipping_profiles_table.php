<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('sender_name', 100);
            $table->string('sender_phone', 30);
            $table->string('default_pickup_address');
            $table->string('default_origin_city', 100);
            $table->string('default_origin_postal_code', 12);
            $table->enum('preferred_service_type', ['regular', 'express', 'same_day']);
            $table->string('preferred_package_type', 50)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_profiles');
    }
};
