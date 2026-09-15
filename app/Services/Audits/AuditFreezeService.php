<?php

namespace App\Services\Audits;

use App\Models\AiModelConfiguration;
use App\Models\Audit;
use App\Models\CulturalTerm;
use App\Models\PromptTemplate;
use App\Models\PromptVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuditFreezeService
{
    /** @param Collection<int, CulturalTerm> $terms @param Collection<int, PromptVersion> $prompts @param Collection<int, AiModelConfiguration> $models */
    public function freeze(Audit $audit, Collection $terms, Collection $prompts, Collection $models): Audit
    {
        return DB::transaction(function () use ($audit, $terms, $prompts, $models): Audit {
            $audit = Audit::query()->lockForUpdate()->findOrFail($audit->getKey());
            abort_unless($audit->status === 'DRAFT', 422, 'Only draft audits can be frozen.');
            abort_unless($terms->isNotEmpty() && $models->isNotEmpty(), 422, 'Audits require selected terms and models.');

            $promptCodes = $prompts->map(fn (PromptVersion $prompt) => PromptTemplate::query()->findOrFail($prompt->prompt_template_id)->code)->sort()->values()->all();
            abort_unless($promptCodes === ['PA', 'PB', 'PC'], 422, 'Exactly PA, PB and PC are required.');

            foreach ($terms->values() as $index => $term) {
                abort_unless($term->document_version_id === $audit->document_version_id && $term->selected_for_audit, 422, 'Terms must be selected from the audit document version.');
                $audit->terms()->create(['cultural_term_id' => $term->id, 'source_phrase_snapshot' => $term->source_phrase, 'source_context_snapshot' => $term->source_context, 'category_id_snapshot' => $term->category_id, 'subcategory_id_snapshot' => $term->subcategory_id, 'sort_order' => $index + 1]);
            }
            foreach ($prompts->values() as $index => $prompt) {
                $code = PromptTemplate::query()->findOrFail($prompt->prompt_template_id)->code;
                $audit->prompts()->create(['prompt_version_id' => $prompt->id, 'code' => $code, 'prompt_body_snapshot' => $prompt->prompt_body, 'sort_order' => $index + 1]);
                $prompt->update(['locked_at' => now()]);
            }
            foreach ($models as $model) {
                abort_unless($model->project_id === $audit->project_id, 422, 'Models must belong to the audit project.');
                $audit->models()->create(['ai_model_configuration_id' => $model->id, 'model_name_snapshot' => $model->display_name, 'provider_model_id_snapshot' => $model->provider_model_id, 'provider_snapshot' => $model->provider, 'parameters_snapshot_json' => ['temperature' => $model->temperature, 'top_p' => $model->top_p, 'max_tokens' => $model->max_tokens, 'seed' => $model->seed, 'parameters' => $model->parameters_json]]);
            }
            $audit->update(['status' => 'READY_TO_GENERATE', 'frozen_at' => now()]);

            return $audit->refresh();
        });
    }
}
