<?php

namespace App\Repositories\Eloquent;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    /**
     * Get all packages with their related data
     * 
     * @param array $filters Optional filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPackages($filters = [])
    {
        $query = Package::with('warehouse');

        // Apply warehouse filter
        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        // Apply category filter
        if (isset($filters['category'])) {
            $packages = $query->get();
            $packages = $packages->filter(function ($package) use ($filters) {
                return $package->getDimensionCategory() === $filters['category'];
            });
            return $packages;
        }

        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('package_status', $filters['status']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get package by ID
     * 
     * @param int $id
     * @return \App\Models\Package
     */
    public function getPackageById($id)
    {
        return Package::with('warehouse')->findOrFail($id);
    }

    /**
     * Create a new package
     * 
     * @param array $data
     * @return \App\Models\Package
     */
    public function createPackage($data)
    {
        // Calculate volume if dimensions are provided
        if (isset($data['length'], $data['width'], $data['height'])) {
            $data['volume'] = $this->calculateVolume(
                $data['length'],
                $data['width'],
                $data['height']
            );
        }

        return Package::create($data);
    }

    /**
     * Update package by ID
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Package
     */
    public function updatePackage($id, $data)
    {
        $package = Package::findOrFail($id);

        // Recalculate volume if dimensions are updated
        if (isset($data['length'], $data['width'], $data['height'])) {
            $data['volume'] = $this->calculateVolume(
                $data['length'],
                $data['width'],
                $data['height']
            );
        }

        $package->update($data);
        return $package->refresh();
    }

    /**
     * Delete package by ID
     * 
     * @param int $id
     * @return bool
     */
    public function deletePackage($id)
    {
        $package = Package::findOrFail($id);
        return $package->delete();
    }

    /**
     * Get packages by warehouse ID
     * 
     * @param int $warehouseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPackagesByWarehouse($warehouseId)
    {
        return Package::where('warehouse_id', $warehouseId)
            ->with('warehouse')
            ->get();
    }

    /**
     * Get package statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $packages = Package::with('warehouse')->get();

        $totalPackages = $packages->count();
        $smallPackages = $packages->filter(function ($p) {
            return $p->getDimensionCategory() === 'small';
        })->count();
        $mediumPackages = $packages->filter(function ($p) {
            return $p->getDimensionCategory() === 'medium';
        })->count();
        $largePackages = $packages->filter(function ($p) {
            return $p->getDimensionCategory() === 'large';
        })->count();

        return [
            'total_packages' => $totalPackages,
            'small_packages' => $smallPackages,
            'medium_packages' => $mediumPackages,
            'large_packages' => $largePackages,
            'by_warehouse' => $packages->groupBy('warehouse_id')->map(function ($group) {
                return [
                    'warehouse_id' => $group->first()->warehouse_id,
                    'warehouse_name' => $group->first()->warehouse->warehouse_name ?? 'N/A',
                    'count' => $group->count(),
                ];
            })->values(),
        ];
    }

    /**
     * Calculate package dimension category
     * 
     * @param array $dimensions
     * @return string
     */
    public function calculateDimensionCategory($dimensions)
    {
        $volume = $this->calculateVolume(
            $dimensions['length'] ?? 0,
            $dimensions['width'] ?? 0,
            $dimensions['height'] ?? 0
        );

        if ($volume <= 1000) {
            return 'small';
        }

        if ($volume <= 5000) {
            return 'medium';
        }

        return 'large';
    }

    /**
     * Calculate package volume
     * 
     * @param int $length
     * @param int $width
     * @param int $height
     * @return int Volume
     */
    public function calculateVolume($length, $width, $height)
    {
        return (int) ($length * $width * $height);
    }

    /**
     * Get packages grouped by category
     * 
     * @return array
     */
    public function getPackagesByCategory()
    {
        $packages = Package::with('warehouse')->get();

        return [
            'small' => $packages->filter(function ($p) {
                return $p->getDimensionCategory() === 'small';
            })->values(),
            'medium' => $packages->filter(function ($p) {
                return $p->getDimensionCategory() === 'medium';
            })->values(),
            'large' => $packages->filter(function ($p) {
                return $p->getDimensionCategory() === 'large';
            })->values(),
        ];
    }
}
