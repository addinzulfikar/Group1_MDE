<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'customer_id',          // ← M3: Customer ownership (REQUIRED)
        'package_id',           // ← M1: Package reference
        'origin_hub_id',        // ← M4: Origin hub
        'destination_hub_id',   // ← M4: Destination hub
        'current_hub_id',       // ← M4: Current location
        'fleet_id',             // ← M4: Assigned fleet
        'status',
        'sent_at',
        'delivered_at'
    ];
    
    // NOTE: No sender_name, receiver_name, weight, dimensions!
    // These are fetched from shipment->package relationship (M1 integration)

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──
    
    /**
     * M3 Integration: Customer who owns this shipment
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * M1 Integration: Package being shipped
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * M4 Integration: Origin warehouse/hub
     */
    public function originHub()
    {
        return $this->belongsTo(Hub::class, 'origin_hub_id');
    }

    /**
     * M4 Integration: Destination warehouse/hub
     */
    public function destinationHub()
    {
        return $this->belongsTo(Hub::class, 'destination_hub_id');
    }

    /**
     * M4 Integration: Current location (realtime tracking)
     */
    public function currentHub()
    {
        return $this->belongsTo(Hub::class, 'current_hub_id');
    }

    /**
     * M4 Integration: Fleet assigned for this shipment
     */
    public function fleet()
    {
        return $this->belongsTo(Fleet::class, 'fleet_id');
    }

    /**
     * M2 Core: Tracking history (status updates & hub transitions)
     */
    public function trackingHistories()
    {
        return $this->hasMany(TrackingHistory::class);
    }

    // Helper methods
    public function getDimensions()
    {
        return "{$this->length} x {$this->width} x {$this->height} cm";
    }

    public function getVolume()
    {
        return ($this->length * $this->width * $this->height) / 1000; // Convert to liters
    }
}
