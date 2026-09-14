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
        Schema::create('mizan3g_source_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_document_id')->constrained('mizan3g_source_documents')->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->longText('text_content');
            $table->string('file_path')->nullable();
            $table->string('content_hash', 64);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['source_document_id', 'version_number']);
            $table->unique(['source_document_id', 'content_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_source_document_versions');
    }
};
