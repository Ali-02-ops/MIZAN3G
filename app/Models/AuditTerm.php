<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditTerm extends Model
{
    protected $table = 'mizan3g_audit_terms';

    protected $guarded = [];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(TermOutput::class);
    }
}
