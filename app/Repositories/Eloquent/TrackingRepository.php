<?php

namespace App\Repositories\Eloquent;

use App\Models\TrackingHistory;
use App\Repositories\Contracts\TrackingRepositoryInterface;

class TrackingRepository implements TrackingRepositoryInterface
{
    protected $model;

    public function __construct(TrackingHistory $model)
    {
        $this->model = $model;
    }

    public function recordHistory($shipmentId, array $data)
    {
        $data['shipment_id'] = $shipmentId;
        $data['recorded_at'] = $data['recorded_at'] ?? now();

        return $this->model->create($data);
    }

    public function getHistoryByShipment($shipmentId)
    {
        return $this->model->where('shipment_id', $shipmentId)
            ->with(['fromHub', 'toHub', 'shipment'])
            ->orderByDesc('recorded_at')
            ->get();
    }

    public function getLatestStatus($shipmentId)
    {
        return $this->model->where('shipment_id', $shipmentId)
            ->latest('recorded_at')
            ->first();
    }
}
