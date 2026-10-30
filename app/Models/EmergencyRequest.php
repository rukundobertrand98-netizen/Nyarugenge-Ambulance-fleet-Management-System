<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $table = 'emergency_requests';

    protected $fillable = [
        'patient_id',
        'dispatcher_id',
        'ambulance_id',

        // NEW STRUCTURED FIELDS
        'patient_phone',
        'additional_notes',

        'pickup_location',
        'pickup_lat',
        'pickup_lng',
        'emergency_type',
        'status',
        'notes',
    ];

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