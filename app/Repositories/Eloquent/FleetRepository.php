<?php

namespace App\Repositories\Eloquent;

use App\Models\Fleet;
use App\Models\FleetLog;
use App\Repositories\Contracts\FleetRepositoryInterface;
use Illuminate\Support\Carbon;

class FleetRepository implements FleetRepositoryInterface
{
    public function getAllFleets($search = null)
    {
        $query = Fleet::with('currentHub')->latest();
        
        if ($search) {
            $query->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
        }
        
        return $query->paginate(15)->withQueryString();
    }

    public function getFleetById($id)
    {
        return Fleet::with(['currentHub', 'logs.originHub', 'logs.destinationHub'])->findOrFail($id);
    }

    public function calculateTransitDuration($fleetId)
    {
        // Get all arrived logs for the fleet to calculate duration
        $logs = FleetLog::with(['originHub', 'destinationHub'])
                        ->where('fleet_id', $fleetId)
                        ->whereNotNull('departed_at')
                        ->whereNotNull('arrived_at')
                        ->latest('arrived_at')
                        ->get();

        $fleet = Fleet::findOrFail($fleetId);

        $transitReports = $logs->map(function ($log) use ($fleet) {
            $departed = Carbon::parse($log->departed_at);
            $arrived = Carbon::parse($log->arrived_at);
            $durationInHours = $departed->diffInHours($arrived);

            return [
                'log_id' => $log->id,
                'origin_hub' => $log->originHub ? $log->originHub->name : 'Unknown (Penugasan Awal)',
                'destination_hub' => $log->destinationHub ? $log->destinationHub->name : 'Unknown',
                'departed_at' => $departed->format('d M y H:i'),
                'arrived_at' => $arrived->format('d M y H:i'),
                'duration_hours' => $durationInHours,
                'packages_carried' => $fleet->capacity, // Simulasi armada selalu membawa muatan sesuai kapasitas
            ];
        });

        $avgDuration = $transitReports->avg('duration_hours');

        return [
            'fleet_id' => $fleet->id,
            'plate_number' => $fleet->plate_number,
            'type' => $fleet->type,
            'average_duration_hours' => round($avgDuration, 2),
            'history' => $transitReports
        ];
    }

    public function storeFleet(array $data)
    {
        return Fleet::create($data);
    }

    public function updateFleetStatus($id, $status)
    {
        $fleet = Fleet::findOrFail($id);
        $oldStatus = $fleet->status;
        $fleet->status = $status;
        $fleet->save();

        if ($oldStatus == 'idle' && $status == 'in_transit') {
            // Armada membawa paket keluar gudang, tumpukan paket (beban) gudang berkurang
            if ($fleet->current_hub_id) {
                \App\Models\Hub::where('id', $fleet->current_hub_id)->decrement('current_load', $fleet->capacity);
            }
        } elseif ($oldStatus == 'in_transit' && $status == 'idle') {
            // Armada tiba dan membongkar muatan, tumpukan paket (beban) gudang bertambah
            if ($fleet->current_hub_id) {
                \App\Models\Hub::where('id', $fleet->current_hub_id)->increment('current_load', $fleet->capacity);
            }
        }

        return $fleet;
    }

    public function relocateFleet($id, $newHubId)
    {
        $fleet = Fleet::findOrFail($id);
        $oldHubId = $fleet->current_hub_id;
        
        if ($oldHubId == $newHubId) return $fleet;

        $oldStatus = $fleet->status;

        $fleet->current_hub_id = $newHubId;
        $fleet->status = 'idle'; // Otomatis sampai di tempat jadi nunggu tugas lagi
        $fleet->save();

        // JIKA TRUK INI MEMANG SEDANG DALAM PERJALANAN (IN TRANSIT) MEMBAWA BARANG
        // MAKA KETIKA TIBA DI LOKASI BARU, BEBAN GUDANG BARU HARUS BERTAMBAH (BONGKAR MUAT)
        if ($oldStatus == 'in_transit') {
            \App\Models\Hub::where('id', $newHubId)->increment('current_load', $fleet->capacity);
        }
        
        // Catat di sistem riwayat pencatatan Fleet Log dengan kolom yang benar!
        \App\Models\FleetLog::create([
            'fleet_id' => $fleet->id,
            'origin_hub_id' => $oldHubId ?: $newHubId, // Mengatasi jika truk tadinya baru diregistrasi tanpa gudang awal
            'destination_hub_id' => $newHubId,
            'status' => 'arrived',
            'departed_at' => now()->subHours(rand(1, 10)), // Pura-puranya perjalanan makan waktu hitungan jam
            'arrived_at' => now()
        ]);

        return $fleet;
    }
}
