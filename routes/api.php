<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Modul 4: Fleet Management & Hub Monitoring
use App\Http\Controllers\API\FleetController;
use App\Http\Controllers\API\HubController;

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
