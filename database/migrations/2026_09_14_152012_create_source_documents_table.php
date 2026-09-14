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
        Schema::create('mizan3g_source_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mizan3g_projects')->restrictOnDelete();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('publication')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('source_language', 16);
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_source_documents');
    }
};
