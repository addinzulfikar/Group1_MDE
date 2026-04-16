<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Modul 1: Warehouse Management
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\PackageController;
// Modul 2: Tracking System (Core)
use App\Http\Controllers\API\TrackingController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('tracking')->group(function () {
    Route::get('/', [TrackingController::class, 'index']); // List all shipments with filters
    Route::post('/', [TrackingController::class, 'store']); // Register new shipment
    Route::get('/search', [TrackingController::class, 'search']); // Search shipments
    Route::get('/{tracking_number}', [TrackingController::class, 'show']); // Get shipment details
    Route::get('/{tracking_number}/history', [TrackingController::class, 'showHistory']); // Get tracking history
    Route::patch('/{tracking_number}/status', [TrackingController::class, 'updateStatus']); // Update shipment status
});

// Modul 4: Fleet Management & Hub Monitoring
use App\Http\Controllers\API\FleetController;
use App\Http\Controllers\API\HubController;

// Modul 4
Route::prefix('fleet')->group(function () {
    Route::get('/', [FleetController::class, 'index']);
    Route::post('/', [FleetController::class, 'store']);
    Route::put('/{id}/status', [FleetController::class, 'updateStatus']);
    Route::put('/{id}/relocate', [FleetController::class, 'relocate']);
    Route::get('/{id}', [FleetController::class, 'show']);
    Route::get('/{id}/duration', [FleetController::class, 'getTransitDuration']);
});

Route::prefix('hub')->group(function () {
    Route::get('/', [HubController::class, 'index']);
    Route::get('/{id}/capacity', [HubController::class, 'checkCapacity']);
});

// Modul 1
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
