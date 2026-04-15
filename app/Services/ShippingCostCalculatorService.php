<?php

namespace App\Services;

use App\Repositories\Contracts\ShippingRateRuleRepositoryInterface;
use RuntimeException;

class ShippingCostCalculatorService
{
    public function __construct(
        protected ShippingRateRuleRepositoryInterface $shippingRateRuleRepository
    ) {
    }

    public function calculate(array $payload): array
    {
        $weightKg = (float) $payload['weight_kg'];
        $distanceKm = (float) $payload['distance_km'];
        $serviceType = (string) $payload['service_type'];
        $isFragile = (bool) ($payload['is_fragile'] ?? false);
        $declaredValue = (float) ($payload['declared_value'] ?? 0);
        $useInsurance = (bool) ($payload['use_insurance'] ?? false);

        $rule = $this->shippingRateRuleRepository->resolveRule($serviceType, $distanceKm);

        if (! $rule) {
            throw new RuntimeException('Aturan ongkir untuk kombinasi layanan dan jarak belum tersedia.');
        }

        $baseCost = $rule->base_price;
        $distanceCost = $rule->price_per_km * $distanceKm;
        $weightCost = $rule->price_per_kg * $weightKg;
        $subtotal = $baseCost + $distanceCost + $weightCost;

        $fuelSurcharge = $subtotal * ($rule->fuel_surcharge_percent / 100);
        $fragileSurcharge = $isFragile ? $rule->fragile_surcharge : 0;
        $insuranceCost = $useInsurance ? ($declaredValue * ($rule->insurance_percent / 100)) : 0;
        $total = $subtotal + $fuelSurcharge + $fragileSurcharge + $insuranceCost;

        return [
            'service_type' => $serviceType,
            'weight_kg' => $weightKg,
            'distance_km' => $distanceKm,
            'cost_breakdown' => [
                'base_cost' => round($baseCost, 2),
                'distance_cost' => round($distanceCost, 2),
                'weight_cost' => round($weightCost, 2),
                'fuel_surcharge' => round($fuelSurcharge, 2),
                'fragile_surcharge' => round($fragileSurcharge, 2),
                'insurance_cost' => round($insuranceCost, 2),
            ],
            'total_cost' => round($total, 2),
            'currency' => 'IDR',
            'estimated_sla_days' => $serviceType === 'same_day' ? 1 : ($serviceType === 'express' ? 2 : 4),
        ];
    }
}
