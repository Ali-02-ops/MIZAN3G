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
        Schema::create('mizan3g_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('mizan3g_organisations')->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('objective')->nullable();
            $table->string('source_language', 16);
            $table->string('target_language', 16);
            $table->string('framework')->default('GHAZALA');
            $table->string('status')->default('DRAFT');
            $table->foreignId('created_by')->constrained('mizan3g_users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['organisation_id', 'slug']);
            $table->index(['organisation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_projects');
    }
};
