<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Main Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {

        $user = Auth::user();

        switch ($user->role) {

            case 'patient':
                return redirect()->route('patient.dashboard');

            case 'dispatcher':
                return redirect()->route('dispatcher.dashboard');

            case 'driver':
                return redirect()->route('driver.dashboard');

            case 'admin':
                return redirect()->route('admin.dashboard');

            default:
                abort(403, 'Unauthorized Access');
        }

    })->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| Patient Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/patient/dashboard',
        [\App\Http\Controllers\Patient\DashboardController::class, 'index']
    )->name('patient.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Submit Emergency Request
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/patient/requests',
        [\App\Http\Controllers\Patient\EmergencyRequestController::class, 'store']
    )->name('patient.requests.store');

    Route::get(
        '/patient/api/tracking',
        [\App\Http\Controllers\Patient\DashboardController::class, 'liveTracking']
    )->name('patient.api.tracking');

});

/*
|--------------------------------------------------------------------------
| Dispatcher Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/dispatcher/dashboard',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'index']
    )->name('dispatcher.dashboard');

    Route::post(
        '/dispatcher/requests/{emergencyRequest}/assign',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'assign']
    )->name('dispatcher.requests.assign');

    Route::post(
        '/dispatcher/requests/{emergencyRequest}/reject',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'reject']
    )->name('dispatcher.requests.reject');

    Route::patch(
        '/dispatcher/requests/{emergencyRequest}/on-route',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'markOnRoute']
    )->name('dispatcher.requests.on-route');

    Route::patch(
        '/dispatcher/requests/{emergencyRequest}/complete',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'complete']
    )->name('dispatcher.requests.complete');

    Route::get(
        '/dispatcher/api/available-ambulances',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'availableAmbulances']
    )->name('dispatcher.api.available-ambulances');

    Route::get(
        '/dispatcher/api/live-tracking',
        [\App\Http\Controllers\Dispatcher\DashboardController::class, 'liveTracking']
    )->name('dispatcher.api.live-tracking');

});

/*
|--------------------------------------------------------------------------
| Driver Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])
        ->name('driver.dashboard');

    Route::get('/driver/api/tracking', [DriverDashboardController::class, 'tracking'])
        ->name('driver.api.tracking');

    Route::post('/driver/api/location', [DriverDashboardController::class, 'updateLocation'])
        ->name('driver.api.location');

    Route::patch('/driver/requests/{emergencyRequest}/on-route', [DriverDashboardController::class, 'markOnRoute'])
        ->name('driver.requests.on-route');

    Route::patch('/driver/requests/{emergencyRequest}/complete', [DriverDashboardController::class, 'complete'])
        ->name('driver.requests.complete');

    Route::patch('/driver/vehicle/status', [DriverDashboardController::class, 'updateVehicle'])
        ->name('driver.vehicle.status');

    Route::post('/driver/maintenance', [DriverDashboardController::class, 'reportMaintenance'])
        ->name('driver.maintenance.store');

});

/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/admin/dashboard',
        [AdminController::class, 'dashboard']
    )->name('admin.dashboard');

    Route::get(
        '/admin/api/dashboard',
        [AdminController::class, 'overview']
    )->name('admin.api.dashboard');

    Route::get(
        '/admin/reports/weekly',
        [AdminController::class, 'downloadWeeklyReport']
    )->name('admin.reports.weekly');

    Route::patch(
        '/admin/api/ambulances/{ambulance}/status',
        [AdminController::class, 'updateAmbulanceStatus']
    )->name('admin.api.ambulances.status');

    Route::post(
        '/admin/users',
        [AdminController::class, 'storeUser']
    )->name('admin.users.store');

    Route::patch(
        '/admin/users/{user}',
        [AdminController::class, 'updateUser']
    )->name('admin.users.update');

    Route::delete(
        '/admin/users/{user}',
        [AdminController::class, 'destroyUser']
    )->name('admin.users.destroy');

    Route::post(
        '/admin/ambulances',
        [AdminController::class, 'storeAmbulance']
    )->name('admin.ambulances.store');

    Route::patch(
        '/admin/ambulances/{ambulance}',
        [AdminController::class, 'updateAmbulance']
    )->name('admin.ambulances.update');

    Route::delete(
        '/admin/ambulances/{ambulance}',
        [AdminController::class, 'destroyAmbulance']
    )->name('admin.ambulances.destroy');

});

/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::redirect('/settings', '/settings/profile');

    Volt::route('settings/profile', 'settings.profile')
        ->name('settings.profile');

    Volt::route('settings/password', 'settings.password')
        ->name('settings.password');

    Volt::route('settings/appearance', 'settings.appearance')
        ->name('settings.appearance');

});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
