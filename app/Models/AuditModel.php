<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditModel extends Model
{
    protected $table = 'mizan3g_audit_models';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['parameters_snapshot_json' => 'array'];
    }
}
