<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceLocation;
use App\Models\EmergencyRequest;

class AmbulanceLocationController extends Controller
{
    public function index()
    {
        $ambulanceLocations = AmbulanceLocation::with('ambulance')->get();

        $patientRequests = EmergencyRequest::whereIn('status', ['pending', 'assigned'])->whereNotNull('pickup_lat')->whereNotNull('pickup_lng')->get(['id','patient_id','pickup_location','pickup_lat','pickup_lng','status']);

        return response()->json([
            'ambulances' => $ambulanceLocations,
            'patient_requests' => $patientRequests,
        ]);
    }
}
