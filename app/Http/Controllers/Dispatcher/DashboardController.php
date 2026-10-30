<?php

namespace App\Http\Controllers\Dispatcher;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\EmergencyRequest;
use App\Models\Trip;
use App\Notifications\DriverAssignedToRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $this->ensureDispatcher();

        $newRequests = EmergencyRequest::with('patient')->where('status', 'pending')->latest()->get();
        $activeRequests = EmergencyRequest::with(['patient', 'ambulance.driver', 'dispatcher'])
            ->whereIn('status', ['assigned', 'on_route'])
            ->latest()
            ->get();
        $completedRequests = EmergencyRequest::with(['patient', 'ambulance'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest()
            ->take(10)
            ->get();
        $availableAmbulances = Ambulance::with('driver')
            ->where('status', 'available')
            ->whereNotNull('driver_id')
            ->orderBy('ambulance_number')
            ->get();
        $ambulances = Ambulance::with(['driver', 'latestLocation'])->orderBy('ambulance_number')->get();

        $stats = [
            'newRequestsCount' => $newRequests->count(),
            'activeTripsCount' => $activeRequests->count(),
            'ambulancesCount' => $ambulances->count(),
            'availableAmbulances' => $availableAmbulances->count(),
            'busyAmbulances' => $ambulances->where('status', 'busy')->count(),
            'maintenanceAmbulances' => $ambulances->whereIn('status', ['maintenance', 'technical_control', 'scheduled_maintenance', 'out_of_service'])->count(),
            'completedToday' => EmergencyRequest::whereDate('updated_at', today())->where('status', 'completed')->count(),
        ];

        return view('dispatcher.dispatcher_dashboard', [
            'newRequests' => $newRequests,
            'activeRequests' => $activeRequests,
            'completedRequests' => $completedRequests,
            'availableAmbulances' => $availableAmbulances,
            'ambulances' => $ambulances,
            'stats' => $stats,
        ]);
    }

    public function assign(Request $request, EmergencyRequest $emergencyRequest)
    {
        $dispatcher = $this->ensureDispatcher();

        $data = $request->validate([
            'ambulance_id' => [
                'required',
                Rule::exists('ambulances', 'id')->where('status', 'available'),
            ],
        ]);

        DB::transaction(function () use ($data, $dispatcher, $emergencyRequest) {
            $requestForUpdate = EmergencyRequest::whereKey($emergencyRequest->id)->lockForUpdate()->firstOrFail();
            $ambulance = Ambulance::with('driver')->whereKey($data['ambulance_id'])->lockForUpdate()->firstOrFail();

            if ($requestForUpdate->status !== 'pending') {
                abort(422, 'This request has already been handled.');
            }

            if ($ambulance->status !== 'available' || ! $ambulance->driver) {
                abort(422, 'Selected ambulance is not available or has no driver.');
            }

            $requestForUpdate->update([
                'dispatcher_id' => $dispatcher->id,
                'ambulance_id' => $ambulance->id,
                'status' => 'assigned',
            ]);

            $ambulance->update(['status' => 'busy']);

            Trip::create([
                'ambulance_id' => $ambulance->id,
                'driver_id' => $ambulance->driver_id,
                'request_id' => $requestForUpdate->id,
                'start_time' => now(),
                'status' => 'active',
            ]);

            $ambulance->driver->notify(new DriverAssignedToRequest($requestForUpdate->fresh('patient')));
        });

        return redirect()->route('dispatcher.dashboard')->with('success', 'Request approved, ambulance assigned, and driver notified.');
    }

    public function reject(Request $request, EmergencyRequest $emergencyRequest)
    {
        $dispatcher = $this->ensureDispatcher();

        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($emergencyRequest->status !== 'pending') {
            return redirect()->route('dispatcher.dashboard')->withErrors(['request' => 'Only pending requests can be rejected.']);
        }

        $emergencyRequest->update([
            'dispatcher_id' => $dispatcher->id,
            'status' => 'cancelled',
            'notes' => trim(($emergencyRequest->notes ? $emergencyRequest->notes."\n" : '').'Rejected by dispatcher: '.($data['reason'] ?? 'No reason provided')),
        ]);

        return redirect()->route('dispatcher.dashboard')->with('success', 'Request rejected successfully.');
    }

    public function markOnRoute(EmergencyRequest $emergencyRequest)
    {
        $this->ensureDispatcher();

        if ($emergencyRequest->status !== 'assigned') {
            return redirect()->route('dispatcher.dashboard')->withErrors(['request' => 'Only assigned requests can be marked on route.']);
        }

        $emergencyRequest->update(['status' => 'on_route']);

        return redirect()->route('dispatcher.dashboard')->with('success', 'Request marked as on route.');
    }

    public function complete(EmergencyRequest $emergencyRequest)
    {
        $this->ensureDispatcher();

        if (! in_array($emergencyRequest->status, ['assigned', 'on_route'], true)) {
            return redirect()->route('dispatcher.dashboard')->withErrors(['request' => 'Only active requests can be completed.']);
        }

        DB::transaction(function () use ($emergencyRequest) {
            $emergencyRequest->update(['status' => 'completed']);

            if ($emergencyRequest->ambulance) {
                $emergencyRequest->ambulance->update(['status' => 'available']);
            }

            Trip::where('request_id', $emergencyRequest->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'completed',
                    'end_time' => now(),
                ]);
        });

        return redirect()->route('dispatcher.dashboard')->with('success', 'Request completed and ambulance released.');
    }

    public function availableAmbulances()
    {
        $this->ensureDispatcher();

        return response()->json([
            'data' => Ambulance::with(['driver:id,name,email,phone,role', 'latestLocation'])
                ->where('status', 'available')
                ->whereNotNull('driver_id')
                ->orderBy('ambulance_number')
                ->get(),
        ]);
    }

    public function liveTracking()
    {
        $this->ensureDispatcher();

        return response()->json([
            'ambulances' => Ambulance::with(['driver:id,name,email,phone,role', 'latestLocation'])
                ->orderBy('ambulance_number')
                ->get(),
            'requests' => EmergencyRequest::with(['patient:id,name,phone', 'ambulance'])
                ->whereIn('status', ['pending', 'assigned', 'on_route'])
                ->latest()
                ->get(),
        ]);
    }

    private function ensureDispatcher()
    {
        $user = Auth::user();

        if ($user?->role !== 'dispatcher') {
            abort(403, 'Unauthorized Access');
        }

        return $user;
    }
}
