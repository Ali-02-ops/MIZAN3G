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
        Schema::create('mizan3g_audit_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->cascadeOnDelete();
            $table->foreignId('ai_model_configuration_id')->constrained('mizan3g_ai_model_configurations')->restrictOnDelete();
            $table->string('model_name_snapshot');
            $table->string('provider_snapshot');
            $table->json('parameters_snapshot_json');
            $table->timestamps();
            $table->unique(['audit_id', 'ai_model_configuration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_audit_models');
    }
};
