<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$views = [
    'driver.driver_dashboard' => [
        'ambulance'        => null,
        'assignedRequests' => collect(),
        'activeTrip'       => null,
        'notifications'    => collect(),
        'maintenanceRecords' => collect(),
    ],
    'admin.admin_dashboard' => [
        'requests'   => collect(),
        'ambulances' => collect(),
        'drivers'    => collect(),
        'users'      => collect(),
        'stats' => [
            'totals' => ['pending_requests'=>0,'active_requests'=>0,'available_ambulances'=>0,
                         'completed_today'=>0,'requests'=>0,'users'=>0,'ambulances'=>0,
                         'maintenance_ambulances'=>0,'technical_control_ambulances'=>0,
                         'scheduled_maintenance_ambulances'=>0,'out_of_service_ambulances'=>0],
            'ambulance_statuses' => ['available'=>0,'busy'=>0,'maintenance'=>0,
                                     'technical_control'=>0,'scheduled_maintenance'=>0,'out_of_service'=>0],
            'role_counts' => ['patients'=>0,'dispatchers'=>0,'drivers'=>0,'admins'=>0],
            'request_trend' => [], 'latest_locations' => [], 'last_updated' => 'now',
        ],
    ],
    'dispatcher.dispatcher_dashboard' => [
        'newRequests'        => collect(),
        'activeRequests'     => collect(),
        'completedRequests'  => collect(),
        'availableAmbulances'=> collect(),
        'ambulances'         => collect(),
        'stats' => ['newRequestsCount'=>0,'activeTripsCount'=>0,'ambulancesCount'=>0,
                    'availableAmbulances'=>0,'busyAmbulances'=>0,'maintenanceAmbulances'=>0,'completedToday'=>0],
    ],
    'patient.Patient_dashboard' => [
        'requests'            => collect(),
        'ambulancesAvailable' => 0,
        'totalRequests'       => 0,
        'pendingRequests'     => 0,
        'completedRequests'   => 0,
        'activeRequest'       => null,
    ],
];

foreach ($views as $view => $data) {
    try {
        view($view, $data)->render();
        echo "OK: $view\n";
    } catch (Throwable $e) {
        echo "ERROR: $view\n  -> " . $e->getMessage() . "\n  -> " . $e->getFile() . ':' . $e->getLine() . "\n";
    }
}
