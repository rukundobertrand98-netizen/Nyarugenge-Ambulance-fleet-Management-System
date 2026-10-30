<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ambulance;

class AmbulanceLocation extends Model
{
    protected $fillable = [
        'ambulance_id',
        'latitude',
        'longitude',
        'speed',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class, 'ambulance_id');
    }
}
