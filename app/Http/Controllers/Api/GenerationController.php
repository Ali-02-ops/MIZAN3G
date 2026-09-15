<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\AuditModel;
use App\Models\AuditPrompt;
use App\Services\Audits\AuditLogService;
use App\Services\Generations\GenerationRunService;
use App\Services\Generations\ImportedGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function queue(Audit $audit, GenerationRunService $service, AuditLogService $logs, Request $request): JsonResponse
    {
        $this->authorize('run', $audit);
        $audit->load(['models', 'prompts']);

        $queued = $service->queue($audit);
        $logs->record($request->user(), $audit->project, 'generation.queued', $audit, new: ['queued_jobs' => $queued], request: $request);

        return response()->json(['queued' => $queued], 202);
    }

    public function import(Request $request, Audit $audit, ImportedGenerationService $service, AuditLogService $logs): JsonResponse
    {
        $this->authorize('run', $audit);
        $data = $request->validate(['audit_model_id' => ['required', 'integer'], 'audit_prompt_id' => ['required', 'integer'], 'raw_response_text' => ['required', 'string'], 'translated_text' => ['required', 'string'], 'analysis_text' => ['nullable', 'string']]);
        $model = AuditModel::query()->whereKey($data['audit_model_id'])->where('audit_id', $audit->id)->firstOrFail();
        $prompt = AuditPrompt::query()->whereKey($data['audit_prompt_id'])->where('audit_id', $audit->id)->firstOrFail();
        $generation = $service->import($audit, $model, $prompt, $data['raw_response_text'], $data['translated_text'], $data['analysis_text'] ?? null);
        $logs->record($request->user(), $audit->project, 'generation.imported', $generation, new: ['audit_model_id' => $model->id, 'audit_prompt_id' => $prompt->id], request: $request);

        return response()->json($generation, 201);
    }

    public function index(Audit $audit): JsonResponse
    {
        $this->authorize('view', $audit);

        return response()->json($audit->generations()->with(['model', 'prompt'])->latest()->get());
    }
}
