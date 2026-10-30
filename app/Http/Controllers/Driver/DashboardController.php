<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceLocation;
use App\Models\AmbulanceMaintenanceRecord;
use App\Models\EmergencyRequest;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = $this->ensureDriver();
        $ambulance = Ambulance::with(['latestLocation', 'maintenanceRecords' => fn ($query) => $query->latest()->take(5)])
            ->where('driver_id', $user->id)
            ->first();

        $assignedRequests = EmergencyRequest::with(['patient', 'ambulance', 'dispatcher'])
            ->whereHas('ambulance', fn ($query) => $query->where('driver_id', $user->id))
            ->whereIn('status', ['assigned', 'on_route'])
            ->latest()
            ->get();

        $activeTrip = Trip::with(['emergencyRequest.patient', 'ambulance'])
            ->where('driver_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $notifications = $user->notifications()->latest()->take(10)->get();
        $maintenanceRecords = $ambulance
            ? $ambulance->maintenanceRecords()->latest()->take(8)->get()
            : collect();

        return view('driver.driver_dashboard', compact(
            'ambulance',
            'assignedRequests',
            'activeTrip',
            'notifications',
            'maintenanceRecords'
        ));
    }

    public function tracking()
    {
        $user = $this->ensureDriver();
        $ambulance = Ambulance::with('latestLocation')->where('driver_id', $user->id)->first();
        $request = EmergencyRequest::with('patient')
            ->whereHas('ambulance', fn ($query) => $query->where('driver_id', $user->id))
            ->whereIn('status', ['assigned', 'on_route'])
            ->latest()
            ->first();

        return response()->json([
            'ambulance' => $ambulance,
            'request' => $request,
            'driver_location' => $ambulance ? [
                'latitude' => $ambulance->latestLocation?->latitude ?? $ambulance->current_lat,
                'longitude' => $ambulance->latestLocation?->longitude ?? $ambulance->current_lng,
            ] : null,
            'patient_location' => $request ? [
                'latitude' => $request->pickup_lat,
                'longitude' => $request->pickup_lng,
                'pickup_location' => $request->pickup_location,
            ] : null,
        ]);
    }

    public function updateLocation(Request $request)
    {
        $user = $this->ensureDriver();
        $ambulance = Ambulance::where('driver_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0|max:300',
        ]);

        $ambulance->update([
            'current_lat' => $data['latitude'],
            'current_lng' => $data['longitude'],
        ]);

        AmbulanceLocation::create([
            'ambulance_id' => $ambulance->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'speed' => $data['speed'] ?? null,
            'recorded_at' => now(),
        ]);

        return response()->json(['message' => 'Location updated successfully.']);
    }

    public function markOnRoute(EmergencyRequest $emergencyRequest)
    {
        $this->authorizeDriverRequest($emergencyRequest);

        if ($emergencyRequest->status !== 'assigned') {
            return redirect()->route('driver.dashboard')->withErrors(['request' => 'Only assigned requests can start pickup.']);
        }

        $emergencyRequest->update(['status' => 'on_route']);

        return redirect()->route('driver.dashboard')->with('success', 'Trip status updated to on route.');
    }

    public function complete(EmergencyRequest $emergencyRequest)
    {
        $this->authorizeDriverRequest($emergencyRequest);

        if (! in_array($emergencyRequest->status, ['assigned', 'on_route'], true)) {
            return redirect()->route('driver.dashboard')->withErrors(['request' => 'This request is not active.']);
        }

        DB::transaction(function () use ($emergencyRequest) {
            $emergencyRequest->update(['status' => 'completed']);
            $emergencyRequest->ambulance?->update(['status' => 'available']);

            Trip::where('request_id', $emergencyRequest->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'completed',
                    'end_time' => now(),
                ]);
        });

        return redirect()->route('driver.dashboard')->with('success', 'Trip completed and vehicle released.');
    }

    public function updateVehicle(Request $request)
    {
        $user = $this->ensureDriver();
        $ambulance = Ambulance::where('driver_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'fuel_level' => 'required|integer|min:0|max:100',
            'battery_level' => 'required|integer|min:0|max:100',
            'overall_condition' => 'required|string|max:255',
        ]);

        $ambulance->update($data);

        return redirect()->route('driver.dashboard')->with('success', 'Vehicle status updated.');
    }

    public function reportMaintenance(Request $request)
    {
        $user = $this->ensureDriver();
        $ambulance = Ambulance::where('driver_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'issue'                   => 'required|string|max:255',
            'vehicle_status'          => 'required|in:maintenance,technical_control,scheduled_maintenance,out_of_service',
            'notes'                   => 'nullable|string|max:1000',
            'next_service_due_at'     => 'nullable|date',
            'technical_control_until' => 'nullable|date',
        ]);

        AmbulanceMaintenanceRecord::create([
            'ambulance_id' => $ambulance->id,
            'driver_id'    => $user->id,
            'issue'        => $data['issue'],
            'notes'        => $data['notes'] ?? null,
            'status'       => 'reported',
        ]);

        $ambulance->update([
            'status'                  => $data['vehicle_status'],
            'maintenance_issue'       => $data['issue'],
            'next_service_due_at'     => $data['next_service_due_at'] ?? $ambulance->next_service_due_at,
            'technical_control_until' => $data['technical_control_until'] ?? $ambulance->technical_control_until,
        ]);

        return redirect()->route('driver.dashboard')->with('success', 'Issue reported and vehicle status updated.');
    }

    private function ensureDriver()
    {
        $user = Auth::user();

        if ($user?->role !== 'driver') {
            abort(403, 'Unauthorized Access');
        }

        return $user;
    }

    private function authorizeDriverRequest(EmergencyRequest $emergencyRequest): void
    {
        $user = $this->ensureDriver();
        $emergencyRequest->loadMissing('ambulance');

        if ($emergencyRequest->ambulance?->driver_id !== $user->id) {
            abort(403, 'Unauthorized Access');
        }
    }
}
