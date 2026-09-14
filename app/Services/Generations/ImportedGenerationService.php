<?php

namespace App\Services\Generations;

use App\Models\Audit;
use App\Models\AuditModel;
use App\Models\AuditPrompt;
use App\Models\Generation;
use App\Models\SourceDocumentVersion;

class ImportedGenerationService
{
    public function import(Audit $audit, AuditModel $model, AuditPrompt $prompt, string $rawResponse, string $translation, ?string $analysis = null): Generation
    {
        abort_unless($audit->status === 'READY_TO_GENERATE', 422, 'Audit must be frozen before importing output.');
        abort_unless($model->audit_id === $audit->id && $prompt->audit_id === $audit->id, 422, 'Generation configuration does not belong to this audit.');

        $attempt = ((int) Generation::query()->where('audit_model_id', $model->id)->where('audit_prompt_id', $prompt->id)->max('attempt_number')) + 1;
        $source = SourceDocumentVersion::query()->findOrFail($audit->document_version_id);

        return Generation::query()->create([
            'audit_id' => $audit->id, 'audit_model_id' => $model->id, 'audit_prompt_id' => $prompt->id,
            'attempt_number' => $attempt, 'replicate_number' => 1, 'submitted_prompt' => $prompt->prompt_body_snapshot,
            'source_text_snapshot' => $source->text_content, 'raw_response_text' => $rawResponse,
            'translated_text' => $translation, 'analysis_text' => $analysis, 'status' => 'COMPLETED',
            'started_at' => now(), 'completed_at' => now(),
        ]);
    }
}
