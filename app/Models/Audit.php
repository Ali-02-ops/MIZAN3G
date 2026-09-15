<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    protected $table = 'mizan3g_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['blind_expert_review' => 'boolean', 'frozen_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function documentVersion(): BelongsTo
    {
        return $this->belongsTo(SourceDocumentVersion::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(AuditTerm::class);
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(AuditPrompt::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(AuditModel::class);
    }

    public function generations(): HasMany
    {
        return $this->hasMany(Generation::class);
    }

    public function expertAssignments(): HasMany
    {
        return $this->hasMany(ExpertAssignment::class);
    }
}
