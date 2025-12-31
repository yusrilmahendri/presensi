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
            if (!Schema::hasColumn('attendances', 'photo')) {
                $table->string('photo')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('attendances', 'device_info')) {
                $table->string('device_info')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('attendances', 'accuracy')) {
                $table->decimal('accuracy', 10, 2)->nullable()->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
