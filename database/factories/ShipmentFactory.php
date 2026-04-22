<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sentAt = fake()->dateTimeBetween('-30 days', 'now');
        $deliveredAt = fake()->randomElement([null, fake()->dateTimeBetween($sentAt, 'now')]);

        return [
            'tracking_number' => 'TRK' . fake()->unique()->numerify('###########'),
            'sender_name' => fake()->name(),
            'sender_phone' => fake()->phoneNumber(),
            'sender_address' => fake()->address(),
            'receiver_name' => fake()->name(),
            'receiver_phone' => fake()->phoneNumber(),
            'receiver_address' => fake()->address(),
            'weight' => fake()->randomFloat(2, 0.5, 50), // 0.5 - 50 kg
            'length' => fake()->numberBetween(5, 200), // 5 - 200 cm
            'width' => fake()->numberBetween(5, 200),
            'height' => fake()->numberBetween(5, 200),
            'origin_hub_id' => fake()->numberBetween(1, 50),
            'destination_hub_id' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['pending', 'in_transit', 'in_hub', 'on_delivery', 'delivered', 'failed']),
            'sent_at' => $sentAt,
            'delivered_at' => $deliveredAt,
        ];
    }

    /**
     * Indicate that the shipment is pending
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'sent_at' => null,
                'delivered_at' => null,
            ];
        });
    }

    /**
     * Indicate that the shipment is delivered
     */
    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
                'sent_at' => fake()->dateTimeBetween('-30 days', '-1 days'),
                'delivered_at' => fake()->dateTimeBetween('-1 days', 'now'),
            ];
        });
    }
}
