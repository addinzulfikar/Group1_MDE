<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    /** @use HasFactory<\Database\Factories\HubFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'current_load',
        'status'
    ];

    public function fleets()
    {
        return $this->hasMany(Fleet::class, 'current_hub_id');
    }
}
