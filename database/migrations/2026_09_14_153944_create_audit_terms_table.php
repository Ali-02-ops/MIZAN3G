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
        Schema::create('mizan3g_audit_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->cascadeOnDelete();
            $table->foreignId('cultural_term_id')->constrained('mizan3g_cultural_terms')->restrictOnDelete();
            $table->string('source_phrase_snapshot');
            $table->longText('source_context_snapshot')->nullable();
            $table->unsignedBigInteger('category_id_snapshot');
            $table->unsignedBigInteger('subcategory_id_snapshot')->nullable();
            $table->unsignedInteger('sort_order');
            $table->timestamps();
            $table->unique(['audit_id', 'cultural_term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_audit_terms');
    }
};
