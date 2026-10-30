<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ambulance_id')
                ->constrained('ambulances')
                ->onDelete('cascade');

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->string('issue');

            $table->text('notes')->nullable();

            $table->enum('status', [
                'reported',
                'pending',
                'in_progress',
                'resolved'
            ])->default('reported');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulance_maintenance_records');
    }
};