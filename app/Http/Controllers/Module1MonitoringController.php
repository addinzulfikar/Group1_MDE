<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Package;

class Module1MonitoringController extends Controller
{
    /**
     * Display the monitoring dashboard for Module 1.
     */
    public function index()
    {
        try {
            // Get all warehouses with relationship
            $warehouses = Warehouse::with('packages')->get();

            // Get all packages with relationship
            $packages = Package::with('warehouse')->get();

            // Calculate warehouse statistics
            $totalWarehouses = $warehouses->count();
            $activeWarehouses = $warehouses->where('status', 'active')->count();
            $totalCapacity = $warehouses->sum('capacity');
            $totalCurrentLoad = $warehouses->sum('current_load');

            // Calculate package statistics
            $totalPackages = $packages->count();
            $packagesByStatus = $packages->groupBy('package_status')->map->count();

            // Calculate package statistics by dimension category
            $packagesByDimension = $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number,
                    'sender_name' => $package->sender_name,
                    'receiver_name' => $package->receiver_name,
                    'origin' => $package->origin,
                    'destination' => $package->destination,
                    'weight' => $package->weight,
                    'volume' => $package->volume,
                    'dimension_category' => $package->getDimensionCategory(),
                    'warehouse_name' => $package->warehouse->warehouse_name ?? 'Unknown',
                    'status' => $package->package_status,
                    'created_at' => $package->created_at->format('Y-m-d H:i:s')
                ];
            });

            $dimensionCategories = $packagesByDimension->groupBy('dimension_category')->map->count();

            // Calculate warehouse usage percentage
            $warehouseUsage = $warehouses->map(function ($warehouse) {
                $usagePercentage = $warehouse->capacity > 0 
                    ? round(($warehouse->current_load / $warehouse->capacity) * 100, 2) 
                    : 0;

                return [
                    'id' => $warehouse->id,
                    'warehouse_code' => $warehouse->warehouse_code,
                    'warehouse_name' => $warehouse->warehouse_name,
                    'location' => $warehouse->location,
                    'capacity' => $warehouse->capacity,
                    'current_load' => $warehouse->current_load,
                    'usage_percentage' => $usagePercentage,
                    'status' => $warehouse->status,
                    'package_count' => $warehouse->packages->count(),
                    'created_at' => $warehouse->created_at->format('Y-m-d H:i:s')
                ];
            });

            // Prepare data for view
            $data = [
                // Warehouse Statistics
                'total_warehouses' => $totalWarehouses,
                'active_warehouses' => $activeWarehouses,
                'total_capacity' => number_format($totalCapacity, 0),
                'total_current_load' => number_format($totalCurrentLoad, 0),
                'overall_usage_percentage' => $totalCapacity > 0 
                    ? round(($totalCurrentLoad / $totalCapacity) * 100, 2) 
                    : 0,

                // Package Statistics
                'total_packages' => $totalPackages,
                'packages_by_status' => $packagesByStatus,
                'packages_by_dimension' => $dimensionCategories,

                // Data Lists
                'warehouses' => $warehouseUsage,
                'packages' => $packagesByDimension,

                // Chart data (for future enhancement)
                'warehouse_codes' => $warehouseUsage->pluck('warehouse_code')->toArray(),
                'warehouse_loads' => $warehouseUsage->pluck('current_load')->toArray(),
            ];

            return view('module1.monitoring', $data);
        } catch (\Exception $e) {
            return view('module1.monitoring')->with('error', 'Failed to load monitoring data: ' . $e->getMessage());
        }
    }
}
