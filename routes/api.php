<?php

use App\Http\Controllers\API\CustomerAuthController;
use App\Http\Controllers\API\ShippingCalculatorController;
use App\Http\Controllers\API\ShippingProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/register', [CustomerAuthController::class, 'register']);
    Route::post('/auth/login', [CustomerAuthController::class, 'login']);

    Route::middleware('customer.auth')->group(function (): void {
        Route::post('/auth/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/customer/shipping-profile', [ShippingProfileController::class, 'show']);
        Route::put('/customer/shipping-profile', [ShippingProfileController::class, 'upsert']);
        Route::post('/customer/shipping-cost/calculate', [ShippingCalculatorController::class, 'calculate']);
    });
});
