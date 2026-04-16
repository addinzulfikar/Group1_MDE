<?php

namespace App\Repositories\Contracts;

interface TrackingRepositoryInterface
{
    public function recordHistory($shipmentId, array $data);
    public function getHistoryByShipment($shipmentId);
    public function getLatestStatus($shipmentId);
}
