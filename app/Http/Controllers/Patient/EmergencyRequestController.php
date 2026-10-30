<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmergencyRequest;
use App\Models\User;

class EmergencyRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'pickup_location' => 'required|string|max:1000',
            'pickup_lat' => 'required',
            'pickup_lng' => 'required',
            'notes' => 'nullable|string|max:2000',
            'emergency_type' => 'nullable|string|max:255',
            'patient_condition' => 'nullable|string|max:255',
        ]);

        $dispatcher = User::where('role', 'dispatcher')->first();

        // Build notes safely (ONLY column that exists in DB)
        $finalNotes = '';

        if (!empty($data['phone'])) {
            $finalNotes .= "Phone: {$data['phone']}\n";
        }

        if (!empty($data['patient_condition'])) {
            $finalNotes .= "Condition: {$data['patient_condition']}\n";
        }

        if (!empty($data['notes'])) {
            $finalNotes .= $data['notes'];
        }

        EmergencyRequest::create([
            'patient_id'       => Auth::id(),
            'dispatcher_id'    => $dispatcher?->id,
            'ambulance_id'     => null,
            'pickup_location'  => $data['pickup_location'],
            'pickup_lat'       => $data['pickup_lat'],
            'pickup_lng'       => $data['pickup_lng'],
            'emergency_type'   => $data['emergency_type'] ?? null,
            'status'           => 'pending',
            'patient_phone'    => $data['phone'] ?? null,
            'notes'            => $data['patient_condition'] ? 'Condition: '.$data['patient_condition'] : null,
            'additional_notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Emergency request sent successfully!');
    }
}