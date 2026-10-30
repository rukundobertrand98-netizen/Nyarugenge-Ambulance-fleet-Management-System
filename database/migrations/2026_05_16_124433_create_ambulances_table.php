<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();

            $table->string('ambulance_number')->unique();

            $table->string('plate_number')->unique();

            $table->enum('status', [
                'available',
                'busy',
                'maintenance'
            ])->default('available');

            $table->decimal('current_lat', 10, 8)->nullable();

            $table->decimal('current_lng', 11, 8)->nullable();

            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulances');
    }
};
