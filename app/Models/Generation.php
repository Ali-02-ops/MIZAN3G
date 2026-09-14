<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $table = 'mizan3g_generations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['raw_request_json' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
