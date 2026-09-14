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
        Schema::create('mizan3g_audit_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->cascadeOnDelete();
            $table->foreignId('prompt_version_id')->constrained('mizan3g_prompt_versions')->restrictOnDelete();
            $table->string('code', 8);
            $table->longText('prompt_body_snapshot');
            $table->unsignedInteger('sort_order');
            $table->timestamps();
            $table->unique(['audit_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_audit_prompts');
    }
};
