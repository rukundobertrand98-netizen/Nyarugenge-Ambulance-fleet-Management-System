<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->unsignedTinyInteger('fuel_level')->default(100)->after('current_lng');
            $table->unsignedTinyInteger('battery_level')->default(100)->after('fuel_level');
            $table->string('overall_condition')->default('Good')->after('battery_level');
            $table->text('maintenance_issue')->nullable()->after('overall_condition');
            $table->date('next_service_due_at')->nullable()->after('maintenance_issue');
            $table->timestamp('technical_control_until')->nullable()->after('next_service_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn([
                'fuel_level',
                'battery_level',
                'overall_condition',
                'maintenance_issue',
                'next_service_due_at',
                'technical_control_until',
            ]);
        });
    }
};
