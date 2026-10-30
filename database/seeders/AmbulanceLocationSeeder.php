<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\AmbulanceLocation;
use Illuminate\Database\Seeder;

class AmbulanceLocationSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Kigali coordinates for testing
        $locations = [
            ['ambulance_id' => 1, 'latitude' => -1.9441, 'longitude' => 30.0619, 'speed' => 0],
            ['ambulance_id' => 2, 'latitude' => -1.9536, 'longitude' => 30.0605, 'speed' => 45.5],
            ['ambulance_id' => 3, 'latitude' => -1.9356, 'longitude' => 30.0631, 'speed' => 0],
        ];

        foreach ($locations as $location) {
            AmbulanceLocation::updateOrCreate(
                ['ambulance_id' => $location['ambulance_id']],
                $location
            );
        }
    }
}
