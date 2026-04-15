<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingProfile extends Model
{
    protected $fillable = [
        'user_id',
        'sender_name',
        'sender_phone',
        'default_pickup_address',
        'default_origin_city',
        'default_origin_postal_code',
        'preferred_service_type',
        'preferred_package_type',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
