<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromptVersion;
use Illuminate\Http\JsonResponse;

class PromptController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PromptVersion::query()
            ->whereNull('locked_at')
            ->with('template:id,code,name')
            ->orderBy('prompt_template_id')
            ->orderByDesc('version_number')
            ->get());
    }
}
