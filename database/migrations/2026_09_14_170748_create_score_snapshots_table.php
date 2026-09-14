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
        Schema::create('mizan3g_score_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('mizan3g_audits')->restrictOnDelete();
            $table->foreignId('audit_model_id')->nullable()->constrained('mizan3g_audit_models')->nullOnDelete();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('scoring_mode');
            $table->decimal('skb', 8, 6)->nullable();
            $table->decimal('ikg', 8, 6)->nullable();
            $table->unsignedInteger('evaluated_units')->default(0);
            $table->unsignedInteger('eligible_terms')->default(0);
            $table->unsignedInteger('unstable_terms')->default(0);
            $table->unsignedInteger('missing_units')->default(0);
            $table->unsignedInteger('imputed_units')->default(0);
            $table->json('calculation_metadata_json');
            $table->timestamp('calculated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mizan3g_score_snapshots');
    }
};
