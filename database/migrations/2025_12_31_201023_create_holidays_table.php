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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('date');
            $table->enum('type', ['national', 'organization', 'religious'])->default('organization');
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false); // untuk libur yang berulang tiap tahun
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['organization_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
