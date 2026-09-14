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
        Schema::create('mizan3g_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->restrictOnDelete();
            $table->foreignId('audit_model_id')->constrained('mizan3g_audit_models')->restrictOnDelete();
            $table->foreignId('audit_prompt_id')->constrained('mizan3g_audit_prompts')->restrictOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->unsignedInteger('replicate_number')->default(1);
            $table->longText('submitted_prompt');
            $table->longText('source_text_snapshot');
            $table->json('raw_request_json')->nullable();
            $table->longText('raw_response_text')->nullable();
            $table->longText('translated_text')->nullable();
            $table->longText('analysis_text')->nullable();
            $table->string('provider_request_id')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->decimal('cost', 12, 6)->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->string('status')->default('PENDING');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['audit_model_id', 'audit_prompt_id', 'attempt_number', 'replicate_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_generations');
    }
};
