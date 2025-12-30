<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_location_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['check_in', 'check_out']);
            $table->timestamp('attendance_time');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('photo')->nullable(); // path to photo
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'attendance_time']);
            $table->index('shift_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

