<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mizan3g_audit_models', fn (Blueprint $table) => $table->string('provider_model_id_snapshot')->nullable()->after('model_name_snapshot'));
    }

    public function down(): void
    {
        Schema::table('mizan3g_audit_models', fn (Blueprint $table) => $table->dropColumn('provider_model_id_snapshot'));
    }
};
