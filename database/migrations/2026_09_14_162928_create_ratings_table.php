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
        Schema::create('mizan3g_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_output_id')->constrained('mizan3g_term_outputs')->restrictOnDelete();
            $table->foreignId('reviewer_user_id')->constrained('mizan3g_users')->restrictOnDelete();
            $table->string('reviewer_role');
            $table->unsignedTinyInteger('rating_value');
            $table->text('rationale')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('DRAFT');
            $table->timestamps();
            $table->unique(['term_output_id', 'reviewer_user_id', 'reviewer_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_ratings');
    }
};
