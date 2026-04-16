<?php

namespace App\Repositories\Contracts;

interface ShipmentRepositoryInterface
{
    public function getAllShipments($search = null, $status = null);
    public function getShipmentById($id);
    public function getShipmentByTrackingNumber($trackingNumber);
    public function searchShipment($keyword);
    public function createShipment(array $data);
    public function updateShipmentStatus($id, $status);
    public function getShipmentHistory($shipmentId);
}
