<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    /**
     * Kolom yang dapat diisi secara massal.
     * warehouse_code dihapus agar selaras dengan tabel hubs (Modul 4).
     * Status menggunakan enum: available, full, overload (sama dengan hubs).
     */
    protected $fillable = [
        'warehouse_name',
        'location',
        'capacity',
        'current_load',
        'status'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_load' => 'integer',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Hitung ulang current_load dari jumlah paket dan update status otomatis.
     * Dipanggil setiap kali paket ditambah atau dihapus dari warehouse ini.
     */
    public function recalculateLoad(): void
    {
        $packageCount = $this->packages()->count();
        $this->current_load = $packageCount;
        $this->status = $this->resolveStatus($packageCount, $this->capacity);
        $this->save();
    }

    /**
     * Tentukan status gudang berdasarkan persentase kapasitas.
     * Selaras dengan logika Modul 4:
     *   available : < 90%
     *   full      : >= 90% dan < 100%
     *   overload  : >= 100%
     */
    public static function resolveStatus(int $load, int $capacity): string
    {
        if ($capacity <= 0) {
            return 'available';
        }
        $percentage = ($load / $capacity) * 100;
        if ($percentage >= 100) {
            return 'overload';
        }
        if ($percentage >= 90) {
            return 'full';
        }
        return 'available';
    }
}