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
        // Add organization_id to shifts table
        Schema::table('shifts', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Add organization_id to attendance_locations table
        Schema::table('attendance_locations', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Add organization_id to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Add organization_id to leaves table
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('attendance_locations', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
