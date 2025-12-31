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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            // Update role enum untuk include super_admin
            $table->enum('role', ['super_admin', 'admin', 'karyawan'])->default('karyawan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
            // Rollback role enum
            $table->enum('role', ['admin', 'karyawan'])->default('karyawan')->change();
        });
    }
};
