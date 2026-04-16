<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Repositories\Contracts\TrackingRepositoryInterface;
use Illuminate\Http\Request;

class TrackingWebController extends Controller
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
     * Dashboard tracking - tampilkan statistik dan list paket
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        try {
            $shipments = $this->shipmentRepo->getAllShipments($search, $status);
            
            // Statistik
            $stats = [
                'total' => \App\Models\Shipment::count(),
                'pending' => \App\Models\Shipment::where('status', 'pending')->count(),
                'in_transit' => \App\Models\Shipment::where('status', 'in_transit')->count(),
                'delivered' => \App\Models\Shipment::where('status', 'delivered')->count(),
                'failed' => \App\Models\Shipment::where('status', 'failed')->count(),
            ];

            return view('tracking.index', compact('shipments', 'stats', 'status', 'search'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Lihat detail paket berdasarkan tracking number
     */
    public function show($trackingNumber)
    {
        try {
            $shipment = $this->shipmentRepo->getShipmentByTrackingNumber($trackingNumber);
            $histories = $this->trackingRepo->getHistoryByShipment($shipment->id);
            $latestHistory = $histories->first();

            return view('tracking.show', compact('shipment', 'histories', 'latestHistory'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Paket tidak ditemukan');
        }
    }

    /**
     * Tampilkan timeline detail paket
     */
    public function timeline($trackingNumber)
    {
        try {
            $shipment = $this->shipmentRepo->getShipmentByTrackingNumber($trackingNumber);
            $histories = $this->trackingRepo->getHistoryByShipment($shipment->id);

            return view('tracking.timeline', compact('shipment', 'histories'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Paket tidak ditemukan');
        }
    }

    /**
     * Form untuk melacak paket (search page)
     */
    public function search()
    {
        return view('tracking.search');
    }

    /**
     * Handle search form submission
     */
    public function doSearch(Request $request)
    {
        $keyword = $request->input('keyword');

        if (!$keyword) {
            return back()->with('error', 'Masukkan kata kunci pencarian');
        }

        try {
            $results = $this->shipmentRepo->searchShipment($keyword);
            return view('tracking.search-results', compact('results', 'keyword'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan pencarian');
        }
    }

    /**
     * API search (untuk autocomplete)
     */
    public function apiSearch(Request $request)
    {
        $q = $request->query('q');

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $results = \App\Models\Shipment::where('tracking_number', 'like', "%$q%")
            ->orWhere('sender_name', 'like', "%$q%")
            ->orWhere('receiver_name', 'like', "%$q%")
            ->orWhere('sender_phone', 'like', "%$q%")
            ->limit(10)
            ->select('id', 'tracking_number', 'sender_name', 'receiver_name', 'status')
            ->get();

        return response()->json($results);
    }
}
