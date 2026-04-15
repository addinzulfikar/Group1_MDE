<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AuthTokenRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected AuthTokenRepositoryInterface $authTokenRepository
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $this->userRepository->createCustomer([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'address' => $payload['address'] ?? null,
            'password' => $payload['password'],
        ]);

        $plainToken = bin2hex(random_bytes(40));
        $this->authTokenRepository->createForUser(
            $user,
            $plainToken,
            $payload['device_name'] ?? 'web-client',
            now()->addDays(30)
        );

        return response()->json([
            'message' => 'Registrasi pelanggan berhasil.',
            'data' => [
                'token' => $plainToken,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                ],
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $this->userRepository->findByEmail($payload['email']);

        if (! $user || ! $user->is_customer || ! Hash::check($payload['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        $plainToken = bin2hex(random_bytes(40));
        $this->authTokenRepository->createForUser(
            $user,
            $plainToken,
            $payload['device_name'] ?? 'web-client',
            now()->addDays(30)
        );

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $plainToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $rawToken = (string) $request->attributes->get('auth_token_raw', '');

        if ($rawToken !== '') {
            $this->authTokenRepository->revokeByToken($rawToken);
        }

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
