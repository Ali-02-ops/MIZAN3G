<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generation extends Model
{
    protected $table = 'mizan3g_generations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['raw_request_json' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AuditModel::class, 'audit_model_id');
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(AuditPrompt::class, 'audit_prompt_id');
    }

    public function termOutputs(): HasMany
    {
        return $this->hasMany(TermOutput::class);
    }
}
