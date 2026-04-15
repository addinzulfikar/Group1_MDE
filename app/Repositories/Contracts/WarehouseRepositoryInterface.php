<?php

namespace App\Repositories\Contracts;

interface WarehouseRepositoryInterface
{
    /**
     * Get all warehouses with their related data
     * 
     * @param array $filters Optional filters (e.g., status, search)
     * @return mixed
     */
    public function getAllWarehouses($filters = []);

    /**
     * Get warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @return mixed
     */
    public function getWarehouseById($id);

    /**
     * Create a new warehouse
     * 
     * @param array $data Warehouse data
     * @return mixed
     */
    public function createWarehouse($data);

    /**
     * Update warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @param array $data Updated data
     * @return mixed
     */
    public function updateWarehouse($id, $data);

    /**
     * Delete warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @return bool
     */
    public function deleteWarehouse($id);

    /**
     * Check if warehouse has packages
     * 
     * @param int $id Warehouse ID
     * @return bool
     */
    public function hasPackages($id);

    /**
     * Get warehouse statistics
     * 
     * @return array Statistics including total, active, capacity, usage
     */
    public function getStatistics();

    /**
     * Calculate warehouse usage percentage
     * 
     * @param int $id Warehouse ID
     * @return float Usage percentage
     */
    public function calculateUsagePercentage($id);
}
