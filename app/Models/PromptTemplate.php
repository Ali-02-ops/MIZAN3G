<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    protected $table = 'mizan3g_prompt_templates';

    protected $fillable = ['code', 'name', 'theoretical_basis', 'orientation', 'is_system_default', 'active'];

    protected function casts(): array
    {
        return ['is_system_default' => 'boolean', 'active' => 'boolean'];
    }
}
