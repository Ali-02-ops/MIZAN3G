<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json(AuditLog::query()->where('project_id', $project->id)->latest()->paginate(50));
    }
}
