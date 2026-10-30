<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ambulances MODIFY status ENUM('available','busy','maintenance','technical_control','scheduled_maintenance','out_of_service') DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::table('ambulances')
            ->whereIn('status', ['technical_control', 'scheduled_maintenance', 'out_of_service'])
            ->update(['status' => 'maintenance']);

        DB::statement("ALTER TABLE ambulances MODIFY status ENUM('available','busy','maintenance') DEFAULT 'available'");
    }
};
