<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of all warehouses.
     */
    public function index()
    {
        try {
            $warehouses = Warehouse::with('packages')->get();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse list retrieved successfully',
                'data' => $warehouses,
                'total' => $warehouses->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve warehouse list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(StoreWarehouseRequest $request)
    {
        try {
            $warehouse = Warehouse::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Warehouse created successfully',
                'data' => $warehouse
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show($id)
    {
        try {
            $warehouse = Warehouse::with('packages')->findOrFail($id);

            // Convert to array for response
            $warehouseData = $warehouse->toArray();
            
            // Ensure 'id' exists
            if (!isset($warehouseData['id'])) {
                $warehouseData['id'] = $warehouse->id;
            }

            // Calculate warehouse usage percentage
            $usagePercentage = $warehouse->capacity > 0 
                ? round(($warehouse->current_load / $warehouse->capacity) * 100, 2) 
                : 0;

            $warehouseData['usage_percentage'] = $usagePercentage;

            return response()->json([
                'success' => true,
                'message' => 'Warehouse retrieved successfully',
                'data' => $warehouseData
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found',
                'error' => 'The warehouse with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(UpdateWarehouseRequest $request, $id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            $warehouse->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Warehouse updated successfully',
                'data' => $warehouse
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found',
                'error' => 'The warehouse with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            // Check if warehouse has packages
            if ($warehouse->packages()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete warehouse',
                    'error' => 'Warehouse has ' . $warehouse->packages()->count() . ' packages. Please remove all packages first.'
                ], 422);
            }

            $warehouse->delete();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse deleted successfully',
                'data' => $warehouse
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found',
                'error' => 'The warehouse with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}