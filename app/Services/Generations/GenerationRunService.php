<?php

namespace App\Services\Generations;

use App\Jobs\GenerateTranslation;
use App\Models\Audit;
use App\Models\Generation;
use App\Models\SourceDocumentVersion;

class GenerationRunService
{
    public function queue(Audit $audit): int
    {
        abort_unless($audit->status === 'READY_TO_GENERATE', 422, 'Audit must be frozen before generation.');
        $source = SourceDocumentVersion::query()->findOrFail($audit->document_version_id);
        $count = 0;
        foreach ($audit->models as $model) {
            foreach ($audit->prompts as $prompt) {
                if ($model->provider_snapshot === 'MANUAL_IMPORT') {
                    continue;
                }
                $generation = Generation::query()->firstOrCreate(['audit_model_id' => $model->id, 'audit_prompt_id' => $prompt->id, 'attempt_number' => 1, 'replicate_number' => 1], ['audit_id' => $audit->id, 'submitted_prompt' => $prompt->prompt_body_snapshot."\n\nSOURCE TEXT:\n".$source->text_content, 'source_text_snapshot' => $source->text_content, 'status' => 'PENDING']);
                if ($generation->wasRecentlyCreated) {
                    GenerateTranslation::dispatch($generation->id);
                    $count++;
                }
            }
        }

        return $count;
    }
}
