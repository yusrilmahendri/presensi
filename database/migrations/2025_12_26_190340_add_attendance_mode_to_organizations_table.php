<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Mode absensi: 'shift' atau 'working_hours'
            $table->enum('attendance_mode', ['shift', 'working_hours'])->default('shift')->after('max_users');
            
            // Konfigurasi untuk mode working_hours
            $table->integer('min_working_hours')->default(8)->after('attendance_mode'); // Jam kerja minimum (contoh: 8 jam)
            $table->integer('grace_period_hours')->default(2)->after('min_working_hours'); // Grace period sebelum lembur (contoh: 2 jam)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['attendance_mode', 'min_working_hours', 'grace_period_hours']);
        });
    }
};
