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
        Schema::table('attendances', function (Blueprint $table) {
            // Ubah shift_id menjadi nullable untuk mendukung working_hours mode
            $table->foreignId('shift_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan shift_id menjadi not null
            $table->foreignId('shift_id')->nullable(false)->change();
        });
    }
};
