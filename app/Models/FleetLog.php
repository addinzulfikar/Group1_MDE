<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FleetLog extends Model
{
    /** @use HasFactory<\Database\Factories\FleetLogFactory> */
    use HasFactory;

    protected $fillable = [
        'fleet_id',
        'origin_hub_id',
        'destination_hub_id',
        'status',
        'departed_at',
        'arrived_at'
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function originHub()
    {
        return $this->belongsTo(Hub::class, 'origin_hub_id');
    }

    public function destinationHub()
    {
        return $this->belongsTo(Hub::class, 'destination_hub_id');
    }
}
