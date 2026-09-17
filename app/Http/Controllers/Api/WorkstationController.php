<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WorkstationAnalysisService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class WorkstationController extends Controller
{
    public function analyze(Request $request, WorkstationAnalysisService $service): JsonResponse
    {
        set_time_limit(180);
        $data = $request->validate(['source_text' => ['required', 'string', 'max:5000'], 'source_language' => ['required', Rule::in(['ms', 'en', 'id', 'ar'])], 'target_language' => ['required', Rule::in(['ms', 'en', 'id', 'ar']), 'different:source_language'], 'analysis_model' => ['required', Rule::in(['qwen', 'gemini', 'deepseek'])]]);
        try { return response()->json($service->analyze($data['source_text'], $data['source_language'], $data['target_language'], $data['analysis_model'])); }
        catch (RequestException $exception) {
            $status = $exception->response->status();
            Log::warning('Workstation provider request failed.', ['status' => $status]);
            $message = in_array($status, [429, 503], true) ? 'The AI provider is busy. Please wait a moment and try again.' : 'The AI provider could not complete the analysis.';
            return response()->json(['message' => $message], 503);
        }
        catch (Throwable $exception) { Log::warning('Workstation analysis failed.', ['exception' => $exception::class]); return response()->json(['message' => 'Analysis could not be completed. Please try again.'], 503); }
    }
}
