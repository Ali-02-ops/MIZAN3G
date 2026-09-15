<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SourceDocumentVersion;
use App\Services\Generations\CulturalTermExtractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermExtractionController extends Controller
{
    public function store(Request $request, SourceDocumentVersion $version, CulturalTermExtractionService $service): JsonResponse
    {
        $this->authorize('update', $version->document->project);

        return response()->json(['proposed_terms' => $service->extract($version, $request->user()->id)]);
    }
}
