<?php

namespace App\Repositories\Contracts;

interface PackageRepositoryInterface
{
    /**
     * Get all packages with their related data
     * 
     * @param array $filters Optional filters (e.g., status, warehouse_id, category)
     * @return mixed
     */
    public function getAllPackages($filters = []);

    /**
     * Get package by ID
     * 
     * @param int $id Package ID
     * @return mixed
     */
    public function getPackageById($id);

    /**
     * Create a new package
     * 
     * @param array $data Package data
     * @return mixed
     */
    public function createPackage($data);

    /**
     * Update package by ID
     * 
     * @param int $id Package ID
     * @param array $data Updated data
     * @return mixed
     */
    public function updatePackage($id, $data);

    /**
     * Delete package by ID
     * 
     * @param int $id Package ID
     * @return bool
     */
    public function deletePackage($id);

    /**
     * Get packages by warehouse ID
     * 
     * @param int $warehouseId Warehouse ID
     * @return mixed
     */
    public function getPackagesByWarehouse($warehouseId);

    /**
     * Get package statistics
     * 
     * @return array Statistics including total packages, by category, by warehouse
     */
    public function getStatistics();

    /**
     * Calculate package dimension category
     * 
     * @param array $dimensions Length, width, height
     * @return string Category (small, medium, large)
     */
    public function calculateDimensionCategory($dimensions);

    /**
     * Calculate package volume
     * 
     * @param int $length Panjang (cm)
     * @param int $width Lebar (cm)
     * @param int $height Tinggi (cm)
     * @return int Volume (cm³)
     */
    public function calculateVolume($length, $width, $height);

    /**
     * Get packages grouped by category
     * 
     * @return array Packages grouped by small, medium, large
     */
    public function getPackagesByCategory();
}
