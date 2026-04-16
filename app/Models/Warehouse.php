<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'location',
        'capacity',
        'current_load',
        'status',
        'hub_id',          // FK ke Modul 4 (Hub) — integrasi antar modul
    ];

    protected $casts = [
        'capacity'     => 'integer',
        'current_load' => 'integer',
        'hub_id'       => 'integer',
    ];

    /**
     * Paket-paket yang disimpan di gudang ini (Modul 1).
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Hub logistik yang menaungi gudang ini (Modul 4).
     * Ketika paket masuk/keluar gudang, hub juga ikut ter-update.
     */
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}