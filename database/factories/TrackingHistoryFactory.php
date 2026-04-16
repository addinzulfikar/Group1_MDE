<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrackingHistory>
 */
class TrackingHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'from_hub_id' => fake()->numberBetween(1, 50),
            'to_hub_id' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['pending', 'in_transit', 'arrived', 'out_for_delivery', 'delivered', 'failed']),
            'notes' => fake()->sentence(),
            'recorded_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
