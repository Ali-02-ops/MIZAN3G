<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptVersion extends Model
{
    protected $table = 'mizan3g_prompt_versions';

    protected $fillable = ['prompt_template_id', 'version_number', 'prompt_body', 'content_hash', 'created_by', 'locked_at', 'supersedes_id'];

    protected function casts(): array
    {
        return ['locked_at' => 'datetime'];
    }
}
