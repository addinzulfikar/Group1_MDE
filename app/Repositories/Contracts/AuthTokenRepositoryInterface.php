<?php

namespace App\Repositories\Contracts;

use App\Models\CustomerApiToken;
use App\Models\User;
use Carbon\CarbonInterface;

interface AuthTokenRepositoryInterface
{
    public function createForUser(User $user, string $plainToken, string $deviceName, ?CarbonInterface $expiresAt): CustomerApiToken;

    public function findActiveByToken(string $plainToken): ?CustomerApiToken;

    public function revokeByToken(string $plainToken): void;
}
