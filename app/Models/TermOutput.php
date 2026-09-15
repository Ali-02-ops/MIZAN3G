<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermOutput extends Model
{
    protected $table = 'mizan3g_term_outputs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['researcher_confirmed' => 'boolean', 'omitted' => 'boolean'];
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    public function auditTerm(): BelongsTo
    {
        return $this->belongsTo(AuditTerm::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
