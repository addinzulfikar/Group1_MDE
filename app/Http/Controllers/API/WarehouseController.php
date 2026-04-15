<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private WarehouseRepositoryInterface $warehouseRepository;

    /**
     * Constructor initialization with dependency injection
     * 
     * @param WarehouseRepositoryInterface $warehouseRepository
     */
    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Display a listing of all warehouses.
     */
    public function index()
    {
        try {
            $warehouses = $this->warehouseRepository->getAllWarehouses();

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
            $warehouse = $this->warehouseRepository->createWarehouse($request->validated());

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
            $warehouse = $this->warehouseRepository->getWarehouseById($id);

            // Convert to array for response
            $warehouseData = $warehouse->toArray();
            
            // Ensure 'id' exists
            if (!isset($warehouseData['id'])) {
                $warehouseData['id'] = $warehouse->id;
            }

            // Calculate warehouse usage percentage using repository method
            $usagePercentage = $this->warehouseRepository->calculateUsagePercentage($warehouse->id);
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
            \Log::error('Warehouse show error: ' . $e->getMessage(), [
                'warehouse_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
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
            $warehouse = $this->warehouseRepository->updateWarehouse($id, $request->validated());
            
            // Convert to array for response
            $warehouseData = $warehouse->toArray();
            
            // Ensure 'id' exists
            if (!isset($warehouseData['id'])) {
                $warehouseData['id'] = $warehouse->id;
            }
            
            // Calculate usage percentage using repository method
            $usagePercentage = $this->warehouseRepository->calculateUsagePercentage($warehouse->id);
            $warehouseData['usage_percentage'] = $usagePercentage;

            return response()->json([
                'success' => true,
                'message' => 'Warehouse updated successfully',
                'data' => $warehouseData
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found',
                'error' => 'The warehouse with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Warehouse update error: ' . $e->getMessage(), [
                'warehouse_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
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
            // Check if warehouse has packages using repository method
            if ($this->warehouseRepository->hasPackages($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete warehouse',
                    'error' => 'Warehouse still has packages. Please remove all packages first.'
                ], 422);
            }

            $this->warehouseRepository->deleteWarehouse($id);

            return response()->json([
                'success' => true,
                'message' => 'Warehouse deleted successfully'
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