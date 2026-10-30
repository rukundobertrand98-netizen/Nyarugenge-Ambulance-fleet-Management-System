<?php

use App\Models\Ambulance;
use App\Models\EmergencyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel backend API is ready for the Nyarugenge ambulance system.',
    ]);
});

Route::get('/ambulances', function () {
    return response()->json([
        'data' => Ambulance::with(['driver:id,name,email,phone,role', 'latestLocation'])->latest()->get(),
    ]);
});

Route::get('/ambulances/{ambulance}', function (Ambulance $ambulance) {
    return response()->json([
        'data' => $ambulance->load(['driver:id,name,email,phone,role', 'latestLocation']),
    ]);
});

Route::get('/emergency-requests', function () {
    return response()->json([
        'data' => EmergencyRequest::with('patient')->latest()->get(),
    ]);
});

Route::post('/emergency-requests', function (Request $request) {
    $data = $request->validate([
        'patient_id' => 'required|integer|exists:users,id',
        'pickup_location' => 'required|string|max:1000',
        'emergency_type' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:2000',
    ]);

    $emergency = EmergencyRequest::create([
        'patient_id' => $data['patient_id'],
        'dispatcher_id' => null,
        'ambulance_id' => null,
        'pickup_location' => $data['pickup_location'],
        'pickup_lat' => 0.0,
        'pickup_lng' => 0.0,
        'emergency_type' => $data['emergency_type'] ?? null,
        'notes' => $data['notes'] ?? null,
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Emergency request created successfully.',
        'data' => $emergency,
    ], 201);
});

// Live map: ambulance and patient locations
Route::get('/ambulance-locations', [\App\Http\Controllers\Api\AmbulanceLocationController::class, 'index']);
