<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\AuthTokenRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerToken
{
    public function __construct(
        protected AuthTokenRepositoryInterface $authTokenRepository
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (! $bearerToken) {
            return response()->json([
                'message' => 'Token autentikasi tidak ditemukan.',
            ], 401);
        }

        $token = $this->authTokenRepository->findActiveByToken($bearerToken);

        if (! $token || ! $token->user || ! $token->user->is_customer) {
            return response()->json([
                'message' => 'Token tidak valid atau sudah tidak aktif.',
            ], 401);
        }

        $token->update(['last_used_at' => now()]);
        Auth::setUser($token->user);
        $request->attributes->set('auth_token_raw', $bearerToken);

        return $next($request);
    }
}
