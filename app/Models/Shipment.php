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
        'sender_name',
        'sender_phone',
        'sender_address',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'weight',
        'length',
        'width',
        'height',
        'origin_hub_id',
        'destination_hub_id',
        'status',
        'sent_at',
        'delivered_at'
    ];

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

    // Relationships
    public function originHub()
    {
        return $this->belongsTo(Hub::class, 'origin_hub_id');
    }

    public function destinationHub()
    {
        return $this->belongsTo(Hub::class, 'destination_hub_id');
    }

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
