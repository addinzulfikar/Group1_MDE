<?php

namespace App\Repositories\Eloquent;

use App\Models\Warehouse;
use App\Repositories\Contracts\WarehouseRepositoryInterface;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    /**
     * Get all warehouses with their related data
     * 
     * @param array $filters Optional filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWarehouses($filters = [])
    {
        $query = Warehouse::with('packages');

        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('warehouse_code', 'like', "%{$search}%")
                  ->orWhere('warehouse_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get warehouse by ID
     * 
     * @param int $id
     * @return \App\Models\Warehouse
     */
    public function getWarehouseById($id)
    {
        return Warehouse::with('packages')->findOrFail($id);
    }

    /**
     * Create a new warehouse
     * 
     * @param array $data
     * @return \App\Models\Warehouse
     */
    public function createWarehouse($data)
    {
        return Warehouse::create($data);
    }

    /**
     * Update warehouse by ID
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Warehouse
     */
    public function updateWarehouse($id, $data)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse->refresh();
    }

    /**
     * Delete warehouse by ID
     * 
     * @param int $id
     * @return bool
     */
    public function deleteWarehouse($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return $warehouse->delete();
    }

    /**
     * Check if warehouse has packages
     * 
     * @param int $id
     * @return bool
     */
    public function hasPackages($id)
    {
        return Warehouse::findOrFail($id)->packages()->exists();
    }

    /**
     * Get warehouse statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $warehouses = Warehouse::with('packages')->get();

        $totalWarehouses = $warehouses->count();
        $activeWarehouses = $warehouses->where('status', 'active')->count();
        $totalPackages = $warehouses->sum(function ($w) {
            return $w->packages->count();
        });
        $totalCapacity = $warehouses->sum('capacity');
        $totalCurrentLoad = $warehouses->sum('current_load');

        return [
            'total_warehouses' => $totalWarehouses,
            'active_warehouses' => $activeWarehouses,
            'inactive_warehouses' => $totalWarehouses - $activeWarehouses,
            'total_packages' => $totalPackages,
            'total_capacity' => $totalCapacity,
            'total_current_load' => $totalCurrentLoad,
            'total_usage_percentage' => $totalCapacity > 0 
                ? round(($totalCurrentLoad / $totalCapacity) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Calculate warehouse usage percentage
     * 
     * @param int $id
     * @return float
     */
    public function calculateUsagePercentage($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        
        if ($warehouse->capacity <= 0) {
            return 0;
        }

        return round(($warehouse->current_load / $warehouse->capacity) * 100, 2);
    }
}
