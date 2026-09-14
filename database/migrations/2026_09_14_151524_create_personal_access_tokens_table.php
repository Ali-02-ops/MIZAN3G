<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sanctum's installer generated this migration after the MIZAN3G-prefixed
        // migration had already been created. It deliberately performs no schema
        // change so the application never creates an unprefixed token table.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // See up(): `mizan3g_personal_access_tokens` is owned by the preceding migration.
    }
};
