<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiModelConfiguration;
use App\Models\Audit;
use App\Models\CulturalTerm;
use App\Models\Project;
use App\Models\PromptVersion;
use App\Models\SourceDocumentVersion;
use App\Services\Audits\AuditFreezeService;
use App\Services\Audits\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuditController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project->audits()->withCount(['terms', 'models'])->latest()->get());
    }

    public function store(Request $request, Project $project, AuditLogService $logs): JsonResponse
    {
        $this->authorize('create', [Audit::class, $project]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'document_version_id' => ['required', 'integer'],
            'audit_mode' => ['required', Rule::in(['PILOT_COMPATIBLE', 'PROFESSIONAL_AUDIT', 'REPEATED_RESEARCH', 'CUSTOM'])],
            'scoring_mode' => ['required', Rule::in(['VERIFIED_ONLY', 'MANUSCRIPT_COMPATIBLE', 'EXPERT_ONLY', 'CONSENSUS'])],
            'blind_expert_review' => ['sometimes', 'boolean'],
        ]);
        $version = SourceDocumentVersion::query()->whereKey($data['document_version_id'])->whereHas('document', fn ($query) => $query->where('project_id', $project->id))->firstOrFail();
        $audit = $project->audits()->create($data + ['document_version_id' => $version->id, 'created_by' => $request->user()->id]);
        $logs->record($request->user(), $project, 'audit.created', $audit, new: ['document_version_id' => $version->id, 'audit_mode' => $audit->audit_mode], request: $request);

        return response()->json($audit, 201);
    }

    public function show(Audit $audit): JsonResponse
    {
        $this->authorize('view', $audit);

        return response()->json($audit->load(['documentVersion.document', 'terms', 'prompts', 'models']));
    }

    public function freeze(Request $request, Audit $audit, AuditFreezeService $service, AuditLogService $logs): JsonResponse
    {
        $this->authorize('update', $audit);
        $data = $request->validate([
            'term_ids' => ['required', 'array', 'min:1'], 'term_ids.*' => ['integer', 'distinct'],
            'prompt_version_ids' => ['required', 'array', 'size:3'], 'prompt_version_ids.*' => ['integer', 'distinct'],
            'model_configuration_ids' => ['required', 'array', 'min:1'], 'model_configuration_ids.*' => ['integer', 'distinct'],
        ]);
        $terms = CulturalTerm::query()->whereIn('id', $data['term_ids'])->where('document_version_id', $audit->document_version_id)->where('selected_for_audit', true)->get();
        abort_unless($terms->count() === count($data['term_ids']), 422, 'Every term must be selected from the audit document version.');
        $prompts = PromptVersion::query()->whereIn('id', $data['prompt_version_ids'])->get();
        abort_unless($prompts->count() === 3, 422, 'Three prompt versions are required.');
        $models = AiModelConfiguration::query()->whereIn('id', $data['model_configuration_ids'])->where('project_id', $audit->project_id)->get();
        abort_unless($models->count() === count($data['model_configuration_ids']), 422, 'Every model must belong to this project.');

        $frozen = $service->freeze($audit, $terms, $prompts, $models);
        $logs->record($request->user(), $audit->project, 'audit.frozen', $frozen, new: ['term_count' => $terms->count(), 'prompt_count' => $prompts->count(), 'model_count' => $models->count()], request: $request);

        return response()->json($frozen->load(['terms', 'prompts', 'models']));
    }
}
