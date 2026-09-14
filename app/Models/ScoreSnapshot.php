<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreSnapshot extends Model
{
    protected $table = 'mizan3g_score_snapshots';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['calculation_metadata_json' => 'array', 'calculated_at' => 'datetime'];
    }
}
