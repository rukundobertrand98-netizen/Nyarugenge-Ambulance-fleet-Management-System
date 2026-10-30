<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ambulance_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->decimal('latitude', 10, 8);

            $table->decimal('longitude', 11, 8);

            $table->decimal('speed', 8, 2)
                  ->nullable();

            $table->timestamp('recorded_at')
                  ->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulance_locations');
    }
};
