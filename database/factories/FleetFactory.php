<?php

namespace Database\Factories;

use App\Models\Fleet;
use App\Models\Hub;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fleet>
 */
class FleetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['truck', 'van', 'motorcycle'];
        $hubId = Hub::query()->inRandomOrder()->value('id');

        if (!$hubId) {
            $hubId = Hub::factory()->create()->id;
        }

        return [
            'plate_number' => 'B-' . fake()->numerify('### ##'),
            'type' => fake()->randomElement($types),
            'capacity' => fake()->numberBetween(500, 5000),
            'status' => fake()->randomElement(['idle', 'in_transit', 'maintenance']),
            'current_hub_id' => $hubId,
        ];
    }
}
