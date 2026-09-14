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
        Schema::create('mizan3g_rating_drift_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained('mizan3g_ratings')->cascadeOnDelete();
            $table->foreignId('drift_type_id')->constrained('mizan3g_drift_types')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['rating_id', 'drift_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_rating_drift_types');
    }
};
