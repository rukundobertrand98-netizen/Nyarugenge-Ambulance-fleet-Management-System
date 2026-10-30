#!/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Ambulance;
use App\Models\AmbulanceLocation;

// Check if ambulances exist
$ambulanceCount = Ambulance::count();
echo "Current ambulances: $ambulanceCount\n";

// Create sample ambulances if none exist
if ($ambulanceCount < 3) {
    echo "Creating sample ambulances...\n";
    for ($i = 1; $i <= 3; $i++) {
        Ambulance::firstOrCreate(
            ['ambulance_number' => "AMB-000$i"],
            [
                'plate_number' => "KV-$i-AMB",
                'status' => $i === 1 ? 'busy' : 'available',
            ]
        );
    }
}

// Create sample locations
echo "Creating sample locations...\n";
$locations = [
    ['ambulance_id' => 1, 'latitude' => -1.9441, 'longitude' => 30.0619, 'speed' => 45.5],
    ['ambulance_id' => 2, 'latitude' => -1.9536, 'longitude' => 30.0605, 'speed' => 0],
    ['ambulance_id' => 3, 'latitude' => -1.9356, 'longitude' => 30.0631, 'speed' => 25.0],
];

foreach ($locations as $location) {
    AmbulanceLocation::updateOrCreate(
        ['ambulance_id' => $location['ambulance_id']],
        $location
    );
}

echo "Done! Ambulances with locations created.\n";
echo "Sample locations:\n";
AmbulanceLocation::with('ambulance')->get()->each(function ($loc) {
    echo "  - Ambulance #{$loc->ambulance->ambulance_number}: {$loc->latitude}, {$loc->longitude}\n";
});
