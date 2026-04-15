<?php

namespace App\Repositories\Eloquent;

use App\Models\ShippingRateRule;
use App\Repositories\Contracts\ShippingRateRuleRepositoryInterface;

class EloquentShippingRateRuleRepository implements ShippingRateRuleRepositoryInterface
{
    public function resolveRule(string $serviceType, float $distanceKm): ?ShippingRateRule
    {
        return ShippingRateRule::query()
            ->where('is_active', true)
            ->where('service_type', $serviceType)
            ->where('min_distance_km', '<=', $distanceKm)
            ->where('max_distance_km', '>=', $distanceKm)
            ->orderByDesc('max_distance_km')
            ->first();
    }
}
