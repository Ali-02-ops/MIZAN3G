<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceDocumentVersion extends Model
{
    use HasFactory;

    protected $table = 'mizan3g_source_document_versions';

    protected $fillable = ['source_document_id', 'version_number', 'text_content', 'file_path', 'content_hash', 'notes', 'created_by'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(SourceDocument::class, 'source_document_id');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(CulturalTerm::class, 'document_version_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
