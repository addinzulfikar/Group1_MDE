<?php

namespace App\Repositories\Eloquent;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    protected $model;

    public function __construct(Shipment $model)
    {
        $this->model = $model;
    }

    public function getAllShipments($search = null, $status = null)
    {
        $query = $this->model->with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories']);

        if ($search) {
            $query->where('tracking_number', 'like', "%$search%")
                ->orWhereHas('package', function ($q) use ($search) {
                    $q->where('sender_name', 'like', "%$search%")
                      ->orWhere('receiver_name', 'like', "%$search%")
                      ->orWhere('origin', 'like', "%$search%")
                      ->orWhere('destination', 'like', "%$search%");
                })
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->paginate(15);
    }

    public function getShipmentById($id)
    {
        return $this->model->with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories'])
            ->findOrFail($id);
    }

    public function getShipmentByTrackingNumber($trackingNumber)
    {
        return $this->model->with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories'])
            ->where('tracking_number', $trackingNumber)
            ->firstOrFail();
    }

    public function searchShipment($keyword)
    {
        return $this->model->with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories'])
            ->where('tracking_number', 'like', "%$keyword%")
            ->orWhereHas('package', function ($q) use ($keyword) {
                $q->where('sender_name', 'like', "%$keyword%")
                  ->orWhere('receiver_name', 'like', "%$keyword%")
                  ->orWhere('origin', 'like', "%$keyword%")
                  ->orWhere('destination', 'like', "%$keyword%");
            })
            ->orWhereHas('customer', function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%");
            })
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function createShipment(array $data)
    {
        // Generate unique tracking number
        $data['tracking_number'] = $this->generateTrackingNumber();
        $data['status'] = 'pending';

        return $this->model->create($data);
    }

    public function updateShipmentStatus($id, $status)
    {
        $shipment = $this->model->findOrFail($id);
        $shipment->update(['status' => $status]);

        if ($status === 'delivered') {
            $shipment->update(['delivered_at' => now()]);
        }

        if ($status === 'in_transit') {
            $shipment->update(['sent_at' => now()]);
        }

        return $shipment;
    }

    public function getShipmentHistory($shipmentId)
    {
        return $this->model->findOrFail($shipmentId)
            ->trackingHistories()
            ->orderByDesc('recorded_at')
            ->get();
    }

    // Helper method
    private function generateTrackingNumber()
    {
        $prefix = 'TRK';
        $timestamp = microtime(true) * 10000;
        $random = rand(1000, 9999);

        return $prefix . substr($timestamp, -10) . $random;
    }
}
