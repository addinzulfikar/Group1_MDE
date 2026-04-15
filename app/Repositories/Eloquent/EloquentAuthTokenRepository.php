<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerApiToken;
use App\Models\User;
use App\Repositories\Contracts\AuthTokenRepositoryInterface;
use Carbon\CarbonInterface;

class EloquentAuthTokenRepository implements AuthTokenRepositoryInterface
{
    public function createForUser(User $user, string $plainToken, string $deviceName, ?CarbonInterface $expiresAt): CustomerApiToken
    {
        return CustomerApiToken::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'device_name' => $deviceName,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findActiveByToken(string $plainToken): ?CustomerApiToken
    {
        $now = now();

        return CustomerApiToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('revoked_at')
            ->where(function ($query) use ($now): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            })
            ->first();
    }

    public function revokeByToken(string $plainToken): void
    {
        CustomerApiToken::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->update([
                'revoked_at' => now(),
            ]);
    }
}
