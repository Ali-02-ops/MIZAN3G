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
        Schema::create('mizan3g_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mizan3g_projects')->restrictOnDelete();
            $table->foreignId('document_version_id')->constrained('mizan3g_source_document_versions')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('audit_mode')->default('PILOT_COMPATIBLE');
            $table->string('scoring_mode')->default('MANUSCRIPT_COMPATIBLE');
            $table->boolean('blind_expert_review')->default(true);
            $table->string('status')->default('DRAFT');
            $table->timestamp('frozen_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_audits');
    }
};
