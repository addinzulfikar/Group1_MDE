<?php

namespace App\Repositories\Contracts;

interface PackageRepositoryInterface
{
    public function getAllPackages($filters = []);
    public function getPackageById($id);
    public function createPackage($data);
    public function updatePackage($id, $data);
    public function deletePackage($id);
    public function getPackagesByWarehouse($warehouseId);
    public function getStatistics();
    public function calculateDimensionCategory($dimensions);
    public function calculateVolume($length, $width, $height);
    public function getPackagesByCategory();
}
