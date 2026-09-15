<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpertAssignment;
use App\Models\Rating;
use App\Models\TermOutput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    public function upsertResearcher(Request $request, TermOutput $termOutput): JsonResponse
    {
        $this->authorize('run', $termOutput->generation->audit);
        abort_unless($termOutput->researcher_confirmed, 422, 'Confirm the extracted term before rating it.');
        $data = $request->validate([
            'rating_value' => ['required', 'integer', 'between:0,2'],
            'rationale' => ['nullable', 'string', 'max:10000'],
            'confidence' => ['nullable', 'numeric', 'between:0,1'],
            'drift_type_ids' => ['sometimes', 'array'],
            'drift_type_ids.*' => ['integer', Rule::exists('mizan3g_drift_types', 'id')->where('active', true)],
            'submit' => ['sometimes', 'boolean'],
        ]);
        $rating = Rating::query()->firstOrNew(['term_output_id' => $termOutput->id, 'reviewer_user_id' => $request->user()->id, 'reviewer_role' => 'RESEARCHER']);
        abort_if($rating->exists && $rating->status === 'SUBMITTED', 422, 'Submitted ratings cannot be overwritten.');
        $rating->fill(collect($data)->except(['drift_type_ids', 'submit'])->all() + ['status' => ! empty($data['submit']) ? 'SUBMITTED' : 'DRAFT']);
        $rating->submitted_at = ! empty($data['submit']) ? now() : null;
        $rating->save();
        $rating->driftTypes()->sync($data['drift_type_ids'] ?? []);

        return response()->json($rating->load('driftTypes'));
    }

    public function upsertExpert(Request $request, TermOutput $termOutput): JsonResponse
    {
        $audit = $termOutput->generation->audit;
        abort_unless(ExpertAssignment::query()->where('audit_id', $audit->id)->where('expert_user_id', $request->user()->id)->where('status', 'ASSIGNED')->exists(), 403);
        abort_unless($termOutput->researcher_confirmed, 422, 'Confirm the extracted term before rating it.');
        $data = $request->validate(['rating_value' => ['required', 'integer', 'between:0, 2'], 'rationale' => ['nullable', 'string', 'max:10000'], 'confidence' => ['nullable', 'numeric', 'between:0, 1'], 'submit' => ['sometimes', 'boolean']]);
        $rating = Rating::query()->firstOrNew(['term_output_id' => $termOutput->id, 'reviewer_user_id' => $request->user()->id, 'reviewer_role' => 'EXPERT']);
        abort_if($rating->exists && $rating->status === 'SUBMITTED', 422, 'Submitted ratings cannot be overwritten.');
        $rating->fill(collect($data)->except('submit')->all() + ['status' => ! empty($data['submit']) ? 'SUBMITTED' : 'DRAFT']);
        $rating->submitted_at = ! empty($data['submit']) ? now() : null;
        $rating->save();

        return response()->json($rating);
    }
}
