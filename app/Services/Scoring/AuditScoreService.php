<?php

namespace App\Services\Scoring;

use App\Domain\Scoring\IKGCalculator;
use App\Domain\Scoring\RatingResolver;
use App\Domain\Scoring\SKBCalculator;
use App\Models\Audit;
use App\Models\ScoreSnapshot;

class AuditScoreService
{
    public function calculate(Audit $audit): array
    {
        $outputs = $audit->generations()->with(['model', 'prompt', 'termOutputs.ratings'])->get()->flatMap(fn ($generation) => $generation->termOutputs->map(fn ($output) => compact('generation', 'output')))->filter(fn ($row) => $row['output']->researcher_confirmed);
        $models = [];
        foreach ($outputs->groupBy(fn ($row) => $row['generation']->audit_model_id) as $modelId => $rows) {
            $ratings = [];
            $imputed = 0;
            $byTerm = [];
            foreach ($rows as $row) {
                $resolved = app(RatingResolver::class)->resolve($row['output']->ratings, $audit->scoring_mode);
                $ratings[] = $resolved['value'] ?? null;
                if ($resolved) {
                    $byTerm[$row['output']->audit_term_id][$row['generation']->prompt->code] = $resolved['value'];
                    $imputed += $resolved['imputed'] ? 1 : 0;
                }
            }
            $models[] = ['audit_model_id' => (int) $modelId, 'name' => $rows->first()['generation']->model->model_name_snapshot, 'skb' => app(SKBCalculator::class)->calculate($ratings), 'ikg' => app(IKGCalculator::class)->calculate($byTerm), 'imputed_units' => $imputed];
        }

        return $models;
    }

    public function snapshot(Audit $audit): array
    {
        $results = $this->calculate($audit);
        foreach ($results as $result) {
            $skb = $result['skb'];
            $ikg = $result['ikg'];
            ScoreSnapshot::query()->create(['audit_id' => $audit->id, 'audit_model_id' => $result['audit_model_id'], 'scope_type' => 'MODEL_OVERALL', 'scoring_mode' => $audit->scoring_mode, 'skb' => $skb['score'] ?? null, 'ikg' => $ikg['score'] ?? null, 'evaluated_units' => $skb['evaluated_units'] ?? 0, 'eligible_terms' => $ikg['eligible_terms'] ?? 0, 'unstable_terms' => $ikg['unstable_terms'] ?? 0, 'missing_units' => $skb['missing_units'] ?? 0, 'imputed_units' => $result['imputed_units'], 'calculation_metadata_json' => ['skb' => $skb, 'ikg' => $ikg], 'calculated_at' => now()]);
        }

        return $results;
    }
}
