<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\EmergencyRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            abort(403, 'Unauthorized Access');
        }

        // Load requests with ambulance (for tracking) — no dispatcher exposed
        $requests = EmergencyRequest::with(['ambulance.driver', 'ambulance.latestLocation'])
            ->where('patient_id', $user->id)
            ->latest()
            ->get();

        $ambulancesAvailable = Ambulance::where('status', 'available')->count();

        $totalRequests    = $requests->count();
        $pendingRequests  = $requests->whereIn('status', ['pending', 'assigned', 'on_route'])->count();
        $completedRequests = $requests->where('status', 'completed')->count();

        // Active request for live tracking (assigned or on_route)
        $activeRequest = $requests->whereIn('status', ['assigned', 'on_route'])->first();

        return view('patient.Patient_dashboard', [
            'requests'           => $requests,
            'ambulancesAvailable'=> $ambulancesAvailable,
            'totalRequests'      => $totalRequests,
            'pendingRequests'    => $pendingRequests,
            'completedRequests'  => $completedRequests,
            'activeRequest'      => $activeRequest,
        ]);
    }

    /**
     * Live tracking API — returns driver position for the patient's active request.
     */
    public function liveTracking()
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            abort(403);
        }

        $activeRequest = EmergencyRequest::with(['ambulance.latestLocation'])
            ->where('patient_id', $user->id)
            ->whereIn('status', ['assigned', 'on_route'])
            ->latest()
            ->first();

        if (! $activeRequest || ! $activeRequest->ambulance) {
            return response()->json([
                'active'         => false,
                'driver_location'=> null,
                'patient_location'=> null,
                'status'         => null,
            ]);
        }

        $amb = $activeRequest->ambulance;
        $loc = $amb->latestLocation;

        return response()->json([
            'active' => true,
            'status' => $activeRequest->status,
            'ambulance_number' => $amb->ambulance_number,
            'driver_location' => [
                'latitude'  => (float) ($loc?->latitude  ?? $amb->current_lat),
                'longitude' => (float) ($loc?->longitude ?? $amb->current_lng),
                'recorded_at' => $loc?->recorded_at?->toDateTimeString(),
            ],
            'patient_location' => [
                'latitude'        => (float) $activeRequest->pickup_lat,
                'longitude'       => (float) $activeRequest->pickup_lng,
                'pickup_location' => $activeRequest->pickup_location,
            ],
        ]);
    }
}
