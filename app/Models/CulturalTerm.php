<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CulturalTerm extends Model
{
    use HasFactory;

    protected $table = 'mizan3g_cultural_terms';

    protected $fillable = ['document_version_id', 'source_phrase', 'source_sentence', 'source_context', 'start_offset', 'end_offset', 'category_id', 'subcategory_id', 'cultural_significance', 'selected_for_audit', 'selection_reason', 'created_by'];

    protected function casts(): array
    {
        return ['selected_for_audit' => 'boolean'];
    }

    public function documentVersion(): BelongsTo
    {
        return $this->belongsTo(SourceDocumentVersion::class, 'document_version_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CulturalCategory::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CulturalSubcategory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
