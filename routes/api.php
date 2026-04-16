<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\ShippingCalculatorController;
use App\Http\Controllers\Api\ShippingProfileController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\FleetController;
use App\Http\Controllers\Api\HubController;

// Legacy sanctum user route (optional)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    // Modul 2: Tracking System (Core)
    Route::prefix('tracking')->group(function () {
        Route::get('/', [TrackingController::class, 'index']); 
        Route::post('/', [TrackingController::class, 'store']); 
        Route::get('/search', [TrackingController::class, 'search']); 
        Route::get('/{tracking_number}', [TrackingController::class, 'show']); 
        Route::get('/{tracking_number}/history', [TrackingController::class, 'showHistory']); 
        Route::patch('/{tracking_number}/status', [TrackingController::class, 'updateStatus']);
    });

    // Modul 3: Authentication
    Route::post('/auth/register', [CustomerAuthController::class, 'register']);
    Route::post('/auth/login', [CustomerAuthController::class, 'login']);

    Route::middleware('customer.auth')->group(function (): void {
        Route::post('/auth/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/customer/shipping-profile', [ShippingProfileController::class, 'show']);
        Route::put('/customer/shipping-profile', [ShippingProfileController::class, 'upsert']);
        Route::post('/customer/shipping-cost/calculate', [ShippingCalculatorController::class, 'calculate']);
    });

    // Modul 1: Warehouse Management
    Route::prefix('warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'index']);
        Route::post('/', [WarehouseController::class, 'store']);
        Route::get('/{id}', [WarehouseController::class, 'show']);
        Route::put('/{id}', [WarehouseController::class, 'update']);
        Route::delete('/{id}', [WarehouseController::class, 'destroy']);
    });

    Route::prefix('package')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::post('/register', [PackageController::class, 'store']);
        Route::get('/{id}', [PackageController::class, 'show']);
        Route::put('/{id}', [PackageController::class, 'update']);
        Route::delete('/{id}', [PackageController::class, 'destroy']);
        Route::get('/{id}/dimension', [PackageController::class, 'getDimension']);
    });

    // Modul 4: Fleet Management & Hub Monitoring
    Route::prefix('fleet')->group(function () {
        Route::get('/', [FleetController::class, 'index']);
        Route::post('/', [FleetController::class, 'store']);
        Route::post('/{id}/load-plan', [FleetController::class, 'calculateLoadPlan']);
        Route::put('/{id}/status', [FleetController::class, 'updateStatus']);
        Route::put('/{id}/relocate', [FleetController::class, 'relocate']);
        Route::get('/{id}', [FleetController::class, 'show']);
        Route::get('/{id}/duration', [FleetController::class, 'getTransitDuration']);
    });

    Route::prefix('hub')->group(function () {
        Route::get('/', [HubController::class, 'index']);
        Route::get('/{id}/capacity', [HubController::class, 'checkCapacity']);
    });
});
