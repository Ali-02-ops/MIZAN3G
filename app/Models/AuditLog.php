<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'mizan3g_audit_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['old_values_json' => 'array', 'new_values_json' => 'array'];
    }
}
