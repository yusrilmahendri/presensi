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
            // Ubah enum status untuk menambahkan 'flexible'
            \DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('on_time', 'late', 'early', 'flexible') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan enum status ke nilai sebelumnya
            \DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('on_time', 'late', 'early') NULL");
        });
    }
};
