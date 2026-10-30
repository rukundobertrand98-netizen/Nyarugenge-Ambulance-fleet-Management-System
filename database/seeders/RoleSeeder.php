<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'patient']);
        Role::create(['name' => 'dispatcher']);
        Role::create(['name' => 'driver']);
        Role::create(['name' => 'admin']);
    }
}