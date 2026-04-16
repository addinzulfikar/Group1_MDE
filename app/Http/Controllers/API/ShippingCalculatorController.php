<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingCalculatorController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
            'distance_km' => ['required', 'numeric', 'min:1'],
            'service_type' => ['required', 'in:regular,express,same_day'],
            'is_fragile' => ['nullable', 'boolean'],
            'use_insurance' => ['nullable', 'boolean'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $weightKg = (float) $payload['weight_kg'];
        $distanceKm = (float) $payload['distance_km'];
        $declaredValue = (float) ($payload['declared_value'] ?? 0);
        $isFragile = (bool) ($payload['is_fragile'] ?? false);
        $useInsurance = (bool) ($payload['use_insurance'] ?? false);

        $serviceMultiplier = match ($payload['service_type']) {
            'express' => 1.4,
            'same_day' => 1.8,
            default => 1.0,
        };

        $baseCost = 8000.0;
        $distanceCost = $distanceKm * 1200.0;
        $weightCost = $weightKg * 2500.0;
        $fuelSurcharge = ($baseCost + $distanceCost) * 0.08;
        $fragileSurcharge = $isFragile ? 5000.0 : 0.0;
        $insuranceCost = $useInsurance ? ($declaredValue * 0.0025) : 0.0;

        $subtotal = ($baseCost + $distanceCost + $weightCost + $fuelSurcharge + $fragileSurcharge + $insuranceCost);
        $totalCost = round($subtotal * $serviceMultiplier, 2);

        $estimatedSlaDays = match ($payload['service_type']) {
            'same_day' => 1,
            'express' => 2,
            default => 3,
        };

        return response()->json([
            'message' => 'Perhitungan ongkir berhasil.',
            'data' => [
                'total_cost' => $totalCost,
                'estimated_sla_days' => $estimatedSlaDays,
                'cost_breakdown' => [
                    'base_cost' => round($baseCost, 2),
                    'distance_cost' => round($distanceCost, 2),
                    'weight_cost' => round($weightCost, 2),
                    'fuel_surcharge' => round($fuelSurcharge, 2),
                    'fragile_surcharge' => round($fragileSurcharge, 2),
                    'insurance_cost' => round($insuranceCost, 2),
                    'service_multiplier' => $serviceMultiplier,
                ],
            ],
        ]);
    }
}
