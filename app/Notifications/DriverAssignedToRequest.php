<?php

namespace App\Notifications;

use App\Models\EmergencyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverAssignedToRequest extends Notification
{
    use Queueable;

    public function __construct(
        private EmergencyRequest $emergencyRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New ambulance pickup assigned',
            'message' => 'You have been assigned to pick up a patient.',
            'request_id' => $this->emergencyRequest->id,
            'pickup_location' => $this->emergencyRequest->pickup_location,
            'pickup_lat' => $this->emergencyRequest->pickup_lat,
            'pickup_lng' => $this->emergencyRequest->pickup_lng,
            'emergency_type' => $this->emergencyRequest->emergency_type,
            'patient_name' => $this->emergencyRequest->patient?->name,
            'patient_phone' => $this->emergencyRequest->patient?->phone,
            'assigned_at' => now()->toDateTimeString(),
        ];
    }
}
