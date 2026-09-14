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
        Schema::create('mizan3g_term_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generation_id')->constrained('mizan3g_generations')->restrictOnDelete();
            $table->foreignId('audit_term_id')->constrained('mizan3g_audit_terms')->restrictOnDelete();
            $table->string('target_expression')->nullable();
            $table->longText('target_context')->nullable();
            $table->string('transliteration')->nullable();
            $table->string('extraction_method')->default('MANUAL');
            $table->decimal('extraction_confidence', 5, 4)->nullable();
            $table->boolean('researcher_confirmed')->default(false);
            $table->boolean('omitted')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['generation_id', 'audit_term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_term_outputs');
    }
};
