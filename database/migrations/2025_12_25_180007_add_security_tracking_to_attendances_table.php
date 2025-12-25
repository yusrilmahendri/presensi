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
            $table->string('device_id')->nullable()->after('photo');
            $table->string('device_model')->nullable()->after('device_id');
            $table->string('device_os')->nullable()->after('device_model');
            $table->string('ip_address', 45)->nullable()->after('device_os');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->boolean('face_detected')->default(false)->after('user_agent');
            $table->integer('face_confidence')->nullable()->after('face_detected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'device_id',
                'device_model',
                'device_os',
                'ip_address',
                'user_agent',
                'face_detected',
                'face_confidence'
            ]);
        });
    }
};
