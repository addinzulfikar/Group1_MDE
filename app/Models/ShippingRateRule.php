<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRateRule extends Model
{
    protected $fillable = [
        'service_type',
        'min_distance_km',
        'max_distance_km',
        'base_price',
        'price_per_km',
        'price_per_kg',
        'fuel_surcharge_percent',
        'fragile_surcharge',
        'insurance_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_distance_km' => 'float',
            'max_distance_km' => 'float',
            'base_price' => 'float',
            'price_per_km' => 'float',
            'price_per_kg' => 'float',
            'fuel_surcharge_percent' => 'float',
            'fragile_surcharge' => 'float',
            'insurance_percent' => 'float',
            'is_active' => 'boolean',
        ];
    }
}
