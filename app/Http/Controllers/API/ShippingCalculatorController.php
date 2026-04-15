<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ShippingCostCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ShippingCalculatorController extends Controller
{
    public function __construct(
        protected ShippingCostCalculatorService $shippingCostCalculatorService
    ) {
    }

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

        try {
            $result = $this->shippingCostCalculatorService->calculate($payload);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Perhitungan ongkir berhasil.',
            'data' => $result,
        ]);
    }
}
