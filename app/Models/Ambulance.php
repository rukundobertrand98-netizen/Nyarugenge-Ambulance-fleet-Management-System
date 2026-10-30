<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    protected $table = 'ambulances';

    protected $fillable = [
        'ambulance_number',
        'plate_number',
        'status',
        'current_lat',
        'current_lng',
        'fuel_level',
        'battery_level',
        'overall_condition',
        'maintenance_issue',
        'next_service_due_at',
        'technical_control_until',
        'driver_id',
    ];

    protected $casts = [
        'next_service_due_at' => 'date',
        'technical_control_until' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function requests()
    {
        return $this->hasMany(EmergencyRequest::class, 'ambulance_id');
    }

    public function locations()
    {
        return $this->hasMany(AmbulanceLocation::class, 'ambulance_id');
    }

    public function latestLocation()
    {
        return $this->hasOne(AmbulanceLocation::class, 'ambulance_id')->latestOfMany('recorded_at');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(AmbulanceMaintenanceRecord::class, 'ambulance_id');
    }
}
