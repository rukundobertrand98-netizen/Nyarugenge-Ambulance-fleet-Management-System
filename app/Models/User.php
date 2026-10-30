<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get user initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn ($name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Driver -> Ambulance
    public function ambulance()
    {
        return $this->hasOne(Ambulance::class, 'driver_id');
    }

    // Patient -> Requests
    public function patientRequests()
    {
        return $this->hasMany(EmergencyRequest::class, 'patient_id');
    }

    // Dispatcher -> Assigned Requests
    public function dispatchedRequests()
    {
        return $this->hasMany(EmergencyRequest::class, 'dispatcher_id');
    }
}
