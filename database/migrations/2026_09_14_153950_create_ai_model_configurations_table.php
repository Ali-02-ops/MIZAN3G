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
        Schema::create('mizan3g_ai_model_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mizan3g_projects')->cascadeOnDelete();
            $table->string('provider');
            $table->string('display_name');
            $table->string('provider_model_id');
            $table->string('execution_environment');
            $table->decimal('temperature', 4, 3)->nullable();
            $table->decimal('top_p', 4, 3)->nullable();
            $table->unsignedInteger('max_tokens')->nullable();
            $table->unsignedBigInteger('seed')->nullable();
            $table->json('parameters_json')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_ai_model_configurations');
    }
};
