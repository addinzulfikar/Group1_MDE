<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\FleetRepositoryInterface;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    protected $fleetRepo;

    public function __construct(FleetRepositoryInterface $fleetRepo)
    {
        $this->fleetRepo = $fleetRepo;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->fleetRepo->getAllFleets()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->fleetRepo->getFleetById($id)
        ]);
    }

    public function getTransitDuration($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->fleetRepo->calculateTransitDuration($id)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|unique:fleets,plate_number',
            'type' => 'required|in:motorcycle,van,truck',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:idle,in_transit,maintenance',
            'current_hub_id' => 'required|exists:hubs,id'
        ]);

        $fleet = $this->fleetRepo->storeFleet($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Armada baru berhasil didaftarkan!',
            'data' => $fleet
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:idle,in_transit,maintenance'
        ]);

        $fleet = $this->fleetRepo->updateFleetStatus($id, $request->status);

        return response()->json([
            'status' => 'success',
            'message' => 'Status armada berhasil diperbarui!',
            'data' => $fleet
        ]);
    }

    public function relocate(Request $request, $id)
    {
        $request->validate([
            'new_hub_id' => 'required|exists:hubs,id'
        ]);

        $fleet = $this->fleetRepo->relocateFleet($id, $request->new_hub_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Armada berhasil direlokasi! Gudang lama dan baru telah diperbarui kapasitasnya.',
            'data' => $fleet
        ]);
    }
}
