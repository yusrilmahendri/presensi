<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop attendance_mode jika ada
        if (Schema::hasColumn('organizations', 'attendance_mode')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropColumn('attendance_mode');
            });
        }
        
        // Tambah enabled_attendance_modes jika belum ada
        if (!Schema::hasColumn('organizations', 'enabled_attendance_modes')) {
            Schema::table('organizations', function (Blueprint $table) {
                // Tambah field baru untuk multiple modes (array JSON)
                $table->json('enabled_attendance_modes')->nullable()->after('max_users');
            });
            
            // Set default value untuk existing records
            DB::table('organizations')->update([
                'enabled_attendance_modes' => json_encode(['shift'])
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('enabled_attendance_modes');
        });
        
        Schema::table('organizations', function (Blueprint $table) {
            $table->enum('attendance_mode', ['shift', 'working_hours'])->default('shift')->after('max_users');
        });
    }
};
