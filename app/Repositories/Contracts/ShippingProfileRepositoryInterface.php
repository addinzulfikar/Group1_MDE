<?php

namespace App\Repositories\Contracts;

use App\Models\ShippingProfile;

interface ShippingProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?ShippingProfile;

    public function upsertForUser(int $userId, array $payload): ShippingProfile;
}
