<?php

namespace App\Services\Documents;

use App\Models\Project;
use App\Models\SourceDocument;
use App\Models\SourceDocumentVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DocumentVersionService
{
    /** @param array<string, mixed> $attributes */
    public function createDocument(Project $project, User $creator, array $attributes): SourceDocument
    {
        return DB::transaction(function () use ($project, $creator, $attributes): SourceDocument {
            $document = SourceDocument::query()->create([
                'project_id' => $project->getKey(), 'title' => $attributes['title'], 'author' => $attributes['author'] ?? null,
                'publication' => $attributes['publication'] ?? null, 'publication_year' => $attributes['publication_year'] ?? null,
                'source_language' => $attributes['source_language'], 'created_by' => $creator->getKey(),
            ]);
            $this->createVersion($document, $creator, $attributes['text_content'], $attributes['file_path'] ?? null, $attributes['notes'] ?? null);

            return $document->refresh();
        });
    }

    public function createVersion(SourceDocument $document, User $creator, string $textContent, ?string $filePath = null, ?string $notes = null): SourceDocumentVersion
    {
        return DB::transaction(function () use ($document, $creator, $textContent, $filePath, $notes): SourceDocumentVersion {
            $document = SourceDocument::query()->lockForUpdate()->findOrFail($document->getKey());
            $version = $document->versions()->create([
                'version_number' => ((int) $document->versions()->max('version_number')) + 1,
                'text_content' => $textContent, 'file_path' => $filePath, 'content_hash' => hash('sha256', $textContent),
                'notes' => $notes, 'created_by' => $creator->getKey(),
            ]);
            $document->update(['current_version_id' => $version->getKey()]);

            return $version;
        });
    }
}
