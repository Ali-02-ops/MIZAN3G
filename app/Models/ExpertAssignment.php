<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertAssignment extends Model
{
    protected $table = 'mizan3g_expert_assignments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_user_id');
    }
}
