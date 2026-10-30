<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceMaintenanceRecord extends Model
{
    protected $table = 'ambulance_maintenance_records';

    protected $fillable = [
        'ambulance_id',
        'driver_id',
        'issue',
        'notes',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}