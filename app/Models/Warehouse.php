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
}