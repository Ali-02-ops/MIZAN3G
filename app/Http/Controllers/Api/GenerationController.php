<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\AuditModel;
use App\Models\AuditPrompt;
use App\Services\Generations\ImportedGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function import(Request $request, Audit $audit, ImportedGenerationService $service): JsonResponse
    {
        $this->authorize('view', $audit->project);
        $data = $request->validate(['audit_model_id' => ['required', 'integer'], 'audit_prompt_id' => ['required', 'integer'], 'raw_response_text' => ['required', 'string'], 'translated_text' => ['required', 'string'], 'analysis_text' => ['nullable', 'string']]);
        $generation = $service->import($audit, AuditModel::query()->findOrFail($data['audit_model_id']), AuditPrompt::query()->findOrFail($data['audit_prompt_id']), $data['raw_response_text'], $data['translated_text'], $data['analysis_text'] ?? null);

        return response()->json($generation, 201);
    }
}
