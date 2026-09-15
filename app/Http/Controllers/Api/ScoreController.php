<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Services\Scoring\AuditScoreService;
use Illuminate\Http\JsonResponse;

class ScoreController extends Controller
{
    public function index(Audit $audit, AuditScoreService $service): JsonResponse
    {
        $this->authorize('view', $audit);

        return response()->json($service->calculate($audit));
    }

    public function snapshot(Audit $audit, AuditScoreService $service): JsonResponse
    {
        $this->authorize('run', $audit);

        return response()->json($service->snapshot($audit), 201);
    }
}
