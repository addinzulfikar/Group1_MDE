<?php

namespace App\Repositories\Eloquent;

use App\Models\ShippingProfile;
use App\Repositories\Contracts\ShippingProfileRepositoryInterface;

class EloquentShippingProfileRepository implements ShippingProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?ShippingProfile
    {
        return ShippingProfile::query()->where('user_id', $userId)->first();
    }

    public function upsertForUser(int $userId, array $payload): ShippingProfile
    {
        return ShippingProfile::query()->updateOrCreate(
            ['user_id' => $userId],
            $payload
        );
    }
}
