<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Repositories\Contracts\TrackingRepositoryInterface;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected $shipmentRepo;
    protected $trackingRepo;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepo,
        TrackingRepositoryInterface $trackingRepo
    ) {
        $this->shipmentRepo = $shipmentRepo;
        $this->trackingRepo = $trackingRepo;
    }

    /**
     * Register shipment dengan tracking number
     * POST /api/tracking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:100',
            'sender_phone' => 'required|string|max:20',
            'sender_address' => 'required|string',
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'origin_hub_id' => 'required|exists:hubs,id',
            'destination_hub_id' => 'required|exists:hubs,id|different:origin_hub_id',
        ]);

        try {
            $shipment = $this->shipmentRepo->createShipment($validated);

            // Record initial history
            $this->trackingRepo->recordHistory($shipment->id, [
                'status' => 'pending',
                'from_hub_id' => $shipment->origin_hub_id,
                'notes' => 'Paket baru telah terdaftar dan menunggu pengiriman'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Paket berhasil didaftarkan!',
                'data' => $shipment->load(['originHub', 'destinationHub', 'trackingHistories'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendaftarkan paket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cari paket berdasarkan tracking number
     * GET /api/tracking/{tracking_number}
     */
    public function show($trackingNumber)
    {
        try {
            $shipment = $this->shipmentRepo->getShipmentByTrackingNumber($trackingNumber);

            return response()->json([
                'status' => 'success',
                'data' => $shipment
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paket dengan nomor resi ' . $trackingNumber . ' tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Dapatkan riwayat status paket (kronologis)
     * GET /api/tracking/{tracking_number}/history
     */
    public function showHistory($trackingNumber)
    {
        try {
            $shipment = $this->shipmentRepo->getShipmentByTrackingNumber($trackingNumber);
            $histories = $this->trackingRepo->getHistoryByShipment($shipment->id);

            return response()->json([
                'status' => 'success',
                'tracking_number' => $shipment->tracking_number,
                'current_status' => $shipment->status,
                'shipment' => $shipment,
                'history' => $histories
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update status paket (hub transition)
     * PATCH /api/tracking/{tracking_number}/status
     */
    public function updateStatus(Request $request, $trackingNumber)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_transit,in_hub,on_delivery,delivered,failed',
            'to_hub_id' => 'nullable|exists:hubs,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $shipment = $this->shipmentRepo->getShipmentByTrackingNumber($trackingNumber);

            // Update shipment status
            $this->shipmentRepo->updateShipmentStatus($shipment->id, $validated['status']);

            // Record history
            $historyData = [
                'status' => $validated['status'],
                'from_hub_id' => $shipment->destination_hub_id, // Or current hub
                'to_hub_id' => $validated['to_hub_id'] ?? null,
                'notes' => $validated['notes'] ?? null
            ];

            $this->trackingRepo->recordHistory($shipment->id, $historyData);

            $shipment = $shipment->fresh()->load(['originHub', 'destinationHub', 'trackingHistories']);

            return response()->json([
                'status' => 'success',
                'message' => 'Status paket berhasil diperbarui!',
                'data' => $shipment
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Cari paket dengan berbagai kriteria
     * GET /api/tracking/search?q=...
     */
    public function search(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json([
                'status' => 'error',
                'message' => 'Masukkan kata kunci pencarian'
            ], 400);
        }

        try {
            $results = $this->shipmentRepo->searchShipment($keyword);

            return response()->json([
                'status' => 'success',
                'keyword' => $keyword,
                'total' => $results->total(),
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pencarian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Daftar semua paket dengan filter
     * GET /api/tracking?status=pending
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        try {
            $shipments = $this->shipmentRepo->getAllShipments($search, $status);

            return response()->json([
                'status' => 'success',
                'total' => $shipments->total(),
                'data' => $shipments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data paket: ' . $e->getMessage()
            ], 500);
        }
    }
}
