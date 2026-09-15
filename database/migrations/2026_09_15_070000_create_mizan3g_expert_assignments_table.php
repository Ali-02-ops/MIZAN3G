<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mizan3g_expert_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->cascadeOnDelete();
            $table->foreignId('expert_user_id')->constrained('mizan3g_users')->restrictOnDelete();
            $table->string('status')->default('ASSIGNED');
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['audit_id', 'expert_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mizan3g_expert_assignments');
    }
};
