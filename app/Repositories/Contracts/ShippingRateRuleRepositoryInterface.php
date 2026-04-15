<?php

namespace App\Repositories\Contracts;

use App\Models\ShippingRateRule;

interface ShippingRateRuleRepositoryInterface
{
    public function resolveRule(string $serviceType, float $distanceKm): ?ShippingRateRule;
}
