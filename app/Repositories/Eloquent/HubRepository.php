<?php

namespace App\Repositories\Eloquent;

use App\Models\Hub;
use App\Repositories\Contracts\HubRepositoryInterface;

class HubRepository implements HubRepositoryInterface
{
    public function getAllHubs($search = null)
    {
        $query = Hub::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        return $query->get();
    }

    public function checkCapacity($hubId)
    {
        $hub = Hub::findOrFail($hubId);
        
        $percentage = ($hub->capacity > 0) ? ($hub->current_load / $hub->capacity) * 100 : 0;
        
        // Return structured data for "monitoring kapasitas gudang"
        $status = 'available';
        if ($percentage >= 100) {
            $status = 'overload';
        } elseif ($percentage >= 90) {
            $status = 'full';
        }

        return [
            'hub_id' => $hub->id,
            'name' => $hub->name,
            'capacity' => $hub->capacity,
            'current_load' => $hub->current_load,
            'utilization_percentage' => round($percentage, 2) . '%',
            'status' => $status
        ];
    }
}
