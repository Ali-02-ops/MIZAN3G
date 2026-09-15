<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Services\Scoring\AuditScoreService;
use Illuminate\Http\JsonResponse;

class ReproducibilityController extends Controller
{
    public function show(Audit $audit, AuditScoreService $scores): JsonResponse
    {
        $this->authorize('view', $audit);
        $audit->load(['project.organisation', 'documentVersion.document', 'terms', 'prompts', 'models', 'generations.termOutputs.ratings.driftTypes']);

        return response()->json([
            'format_version' => '1.0',
            'generated_at' => now()->toIso8601String(),
            'disclaimer' => 'MIZAN3G scores are descriptive audit measures. They depend on the frozen corpus, selected terms, prompts, model environment, and submitted ratings.',
            'audit' => $audit,
            'scores' => $scores->calculate($audit),
        ]);
    }
}
