<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermOutput extends Model
{
    protected $table = 'mizan3g_term_outputs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['researcher_confirmed' => 'boolean', 'omitted' => 'boolean'];
    }
}
