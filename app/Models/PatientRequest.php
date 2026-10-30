<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientRequest extends Model
{
    protected $table = 'patient_requests';

    protected $fillable = [
        'patient_id',
        'dispatcher_id',
        'ambulance_id',
        'phone',
        'pickup_location',
        'pickup_lat',
        'pickup_lng',
        'emergency_type',
        'patient_condition',
        'notes',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class, 'ambulance_id');
    }
}