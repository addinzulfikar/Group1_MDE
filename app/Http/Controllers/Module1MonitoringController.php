<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;

class Module1MonitoringController extends Controller
{
    private WarehouseRepositoryInterface $warehouseRepository;
    private PackageRepositoryInterface $packageRepository;

    /**
     * Constructor initialization with dependency injection
     * 
     * @param WarehouseRepositoryInterface $warehouseRepository
     * @param PackageRepositoryInterface $packageRepository
     */
    public function __construct(
        WarehouseRepositoryInterface $warehouseRepository,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->warehouseRepository = $warehouseRepository;
        $this->packageRepository = $packageRepository;
    }

    /**
     * Display the monitoring dashboard for Module 1.
     */
    public function index()
    {
        try {
            // Get warehouse and package statistics from repositories
            $warehouseStats = $this->warehouseRepository->getStatistics();
            $packageStats = $this->packageRepository->getStatistics();

            // Get all warehouses with usage percentages
            $warehouses = $this->warehouseRepository->getAllWarehouses();
            $warehouseUsage = $warehouses->map(function ($warehouse) {
                $usagePercentage = $this->warehouseRepository->calculateUsagePercentage($warehouse->id);

                return [
                    'id'               => $warehouse->id,
                    'warehouse_name'   => $warehouse->warehouse_name,
                    'location'         => $warehouse->location,
                    'capacity'         => $warehouse->capacity,
                    'current_load'     => $warehouse->current_load,
                    'usage_percentage' => $usagePercentage,
                    'status'           => $warehouse->status,
                    'package_count'    => $warehouse->packages->count(),
                    'created_at'       => $warehouse->created_at->format('Y-m-d H:i:s'),
                ];
            });

            // Get all packages with dimension categories
            $packages = $this->packageRepository->getAllPackages();
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

            // Get packages grouped by category
            $packagesByCategory = $this->packageRepository->getPackagesByCategory();
            $dimensionCategories = [
                'small' => $packageStats['small_packages'],
                'medium' => $packageStats['medium_packages'],
                'large' => $packageStats['large_packages'],
            ];

            // Prepare data for view
            $data = [
                // Warehouse Statistics
                'total_warehouses'       => $warehouseStats['total_warehouses'],
                'available_warehouses'   => $warehouseStats['available_warehouses'],
                'full_warehouses'        => $warehouseStats['full_warehouses'],
                'overload_warehouses'    => $warehouseStats['overload_warehouses'],
                'total_capacity'         => number_format($warehouseStats['total_capacity'], 0),
                'total_current_load'     => number_format($warehouseStats['total_current_load'], 0),
                'overall_usage_percentage' => $warehouseStats['total_usage_percentage'],

                // Package Statistics
                'total_packages'         => $packageStats['total_packages'],
                'packages_by_dimension'  => $dimensionCategories,

                // Data Lists
                'warehouses'             => $warehouseUsage,
                'packages'               => $packagesByDimension,

                // Chart data
                'warehouse_names'        => $warehouseUsage->pluck('warehouse_name')->toArray(),
                'warehouse_loads'        => $warehouseUsage->pluck('current_load')->toArray(),
            ];

            return view('module1.monitoring', $data);
        } catch (\Exception $e) {
            return view('module1.monitoring')->with('error', 'Failed to load monitoring data: ' . $e->getMessage());
        }
    }
}
