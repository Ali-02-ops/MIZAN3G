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
        Schema::create('mizan3g_cultural_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_version_id')->constrained('mizan3g_source_document_versions')->restrictOnDelete();
            $table->string('source_phrase');
            $table->text('source_sentence')->nullable();
            $table->longText('source_context')->nullable();
            $table->unsignedInteger('start_offset')->nullable();
            $table->unsignedInteger('end_offset')->nullable();
            $table->foreignId('category_id')->constrained('mizan3g_cultural_categories')->restrictOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('mizan3g_cultural_subcategories')->restrictOnDelete();
            $table->text('cultural_significance')->nullable();
            $table->boolean('selected_for_audit')->default(false);
            $table->text('selection_reason')->nullable();
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['document_version_id', 'selected_for_audit']);
            $table->index(['category_id', 'subcategory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_cultural_terms');
    }
};
