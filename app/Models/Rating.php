<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rating extends Model
{
    protected $table = 'mizan3g_ratings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function termOutput(): BelongsTo
    {
        return $this->belongsTo(TermOutput::class);
    }

    public function driftTypes(): BelongsToMany
    {
        return $this->belongsToMany(DriftType::class, 'mizan3g_rating_drift_types');
    }
}
