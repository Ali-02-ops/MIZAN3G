<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'mizan3g_ratings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }
}
