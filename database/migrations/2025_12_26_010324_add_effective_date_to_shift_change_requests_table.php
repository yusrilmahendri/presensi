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
        Schema::table('shift_change_requests', function (Blueprint $table) {
            $table->date('effective_date')->nullable()->after('requested_shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_change_requests', function (Blueprint $table) {
            $table->dropColumn('effective_date');
        });
    }
};
