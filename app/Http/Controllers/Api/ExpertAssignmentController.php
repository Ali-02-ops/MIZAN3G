<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrganisationRole;
use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\ExpertAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(ExpertAssignment::query()->where('expert_user_id', $request->user()->id)->with('audit.project')->latest()->get());
    }

    public function store(Request $request, Audit $audit): JsonResponse
    {
        $this->authorize('run', $audit);
        $data = $request->validate(['expert_user_id' => ['required', 'integer']]);
        $expert = User::query()->findOrFail($data['expert_user_id']);
        abort_unless($expert->hasOrganisationRole($audit->project->organisation, [OrganisationRole::ExpertReviewer]), 422, 'The user is not an expert reviewer in this organisation.');
        $assignment = ExpertAssignment::query()->updateOrCreate(['audit_id' => $audit->id, 'expert_user_id' => $expert->id], ['status' => 'ASSIGNED', 'assigned_at' => now(), 'completed_at' => null]);

        return response()->json($assignment, 201);
    }
}
