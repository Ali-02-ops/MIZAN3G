<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditTerm;
use App\Models\Generation;
use App\Models\TermOutput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TermOutputController extends Controller
{
    public function index(Generation $generation): JsonResponse
    {
        $this->authorize('view', $generation->audit);

        return response()->json($generation->termOutputs()->with('auditTerm')->get());
    }

    public function store(Request $request, Generation $generation): JsonResponse
    {
        $this->authorize('run', $generation->audit);
        $data = $this->validated($request, true);
        $term = AuditTerm::query()->whereKey($data['audit_term_id'])->where('audit_id', $generation->audit_id)->firstOrFail();
        $output = $generation->termOutputs()->updateOrCreate(['audit_term_id' => $term->id], $data);

        return response()->json($output->load('auditTerm'), 201);
    }

    public function update(Request $request, TermOutput $termOutput): JsonResponse
    {
        $this->authorize('run', $termOutput->generation->audit);
        $termOutput->update($this->validated($request, false));

        return response()->json($termOutput->fresh()->load('auditTerm'));
    }

    private function validated(Request $request, bool $create): array
    {
        return $request->validate([
            'audit_term_id' => [$create ? 'required' : 'sometimes', 'integer'],
            'target_expression' => ['nullable', 'string', 'max:1000'],
            'target_context' => ['nullable', 'string', 'max:50000'],
            'transliteration' => ['nullable', 'string', 'max:1000'],
            'extraction_method' => [$create ? 'required' : 'sometimes', Rule::in(['MANUAL', 'AI_ASSISTED'])],
            'extraction_confidence' => ['nullable', 'numeric', 'between:0,1'],
            'researcher_confirmed' => ['sometimes', 'boolean'],
            'omitted' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
