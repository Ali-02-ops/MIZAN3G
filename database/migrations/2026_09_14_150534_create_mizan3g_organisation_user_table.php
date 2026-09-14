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
        Schema::create('mizan3g_organisation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('mizan3g_organisations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('mizan3g_users')->cascadeOnDelete();
            $table->string('role');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['organisation_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_organisation_user');
    }
};
