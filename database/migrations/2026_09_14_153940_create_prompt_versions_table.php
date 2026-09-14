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
        Schema::create('mizan3g_prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_template_id')->constrained('mizan3g_prompt_templates')->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->longText('prompt_body');
            $table->string('content_hash', 64);
            $table->foreignId('created_by')->nullable()->constrained('mizan3g_users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->unsignedBigInteger('supersedes_id')->nullable();
            $table->timestamps();
            $table->unique(['prompt_template_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_prompt_versions');
    }
};
