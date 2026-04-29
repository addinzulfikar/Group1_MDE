<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'tracking_number',
        'sender_name',
        'receiver_name',
        'origin',
        'destination',
        'weight',
        'length',
        'width',
        'height',
        'volume',
        'warehouse_id',
        'package_status'
    ];

    protected $casts = [
        'weight' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'volume' => 'float',
        'warehouse_id' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * M2 Integration: Shipment for this package (one-to-one)
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'package_id');
    }

    public function getDimensionCategory()
    {
        if ($this->volume <= 1000) {
            return 'small';
        }

        if ($this->volume <= 5000) {
            return 'medium';
        }

        return 'large';
    }
}