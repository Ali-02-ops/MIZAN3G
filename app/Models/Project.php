<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organisation_id', 'name', 'slug', 'description', 'objective', 'source_language', 'target_language', 'framework', 'status', 'created_by'])]
class Project extends Model
{
    use HasFactory;

    protected $table = 'mizan3g_projects';

    protected function casts(): array
    {
        return ['status' => ProjectStatus::class];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
