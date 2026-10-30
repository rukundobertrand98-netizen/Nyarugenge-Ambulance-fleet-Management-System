<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ambulance_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('driver_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('request_id')
                  ->constrained('emergency_requests')
                  ->onDelete('cascade');

            $table->timestamp('start_time')->nullable();

            $table->timestamp('end_time')->nullable();

            $table->enum('status', [
                'active',
                'completed',
                'cancelled'
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};