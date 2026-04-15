<?php

namespace Tests\Feature;

use App\Models\CustomerApiToken;
use App\Models\User;
use Database\Seeders\ShippingRateRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Module3TrackingSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'address' => 'Bandung',
            'device_name' => 'android-budi',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'budi@example.com')
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'phone', 'address'],
                ],
            ]);
    }

    public function test_customer_can_calculate_shipping_cost_with_valid_token(): void
    {
        $this->seed(ShippingRateRuleSeeder::class);
        $token = $this->createTokenForCustomer();

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/customer/shipping-cost/calculate', [
                'weight_kg' => 3.5,
                'distance_km' => 120,
                'service_type' => 'express',
                'is_fragile' => true,
                'use_insurance' => true,
                'declared_value' => 500000,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.service_type', 'express')
            ->assertJsonPath('data.currency', 'IDR');
    }

    public function test_customer_can_create_or_update_shipping_profile(): void
    {
        $token = $this->createTokenForCustomer();

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/customer/shipping-profile', [
                'sender_name' => 'Budi Santoso',
                'sender_phone' => '081234567890',
                'default_pickup_address' => 'Jl. Asia Afrika No. 10',
                'default_origin_city' => 'Bandung',
                'default_origin_postal_code' => '40111',
                'preferred_service_type' => 'regular',
                'preferred_package_type' => 'box',
                'notes' => 'Pickup jam 09:00 - 12:00',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.default_origin_city', 'Bandung')
            ->assertJsonPath('data.preferred_service_type', 'regular');
    }

    private function createTokenForCustomer(): string
    {
        $user = User::factory()->create([
            'is_customer' => true,
        ]);

        $plainToken = bin2hex(random_bytes(40));

        CustomerApiToken::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'device_name' => 'test-suite',
            'expires_at' => now()->addDay(),
        ]);

        return $plainToken;
    }
}
