<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ShippingProfileRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingProfileController extends Controller
{
    public function __construct(
        protected ShippingProfileRepositoryInterface $shippingProfileRepository
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $this->shippingProfileRepository->findByUserId((int) $user->id);

        return response()->json([
            'message' => 'Profil pengiriman pelanggan.',
            'data' => $profile,
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'sender_name' => ['required', 'string', 'max:100'],
            'sender_phone' => ['required', 'string', 'max:30'],
            'default_pickup_address' => ['required', 'string', 'max:255'],
            'default_origin_city' => ['required', 'string', 'max:100'],
            'default_origin_postal_code' => ['required', 'string', 'max:12'],
            'preferred_service_type' => ['required', 'in:regular,express,same_day'],
            'preferred_package_type' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $profile = $this->shippingProfileRepository->upsertForUser((int) $request->user()->id, $payload);

        return response()->json([
            'message' => 'Profil pengiriman berhasil disimpan.',
            'data' => $profile,
        ]);
    }
}
