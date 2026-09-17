<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModelConfigurationController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project->modelConfigurations()->latest()->get());
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);
        $data = $request->validate([
            'provider' => ['required', Rule::in(['OPENAI', 'ANTHROPIC', 'GEMINI', 'DEEPSEEK', 'OLLAMA', 'MANUAL_IMPORT'])],
            'display_name' => ['required', 'string', 'max:255'],
            'provider_model_id' => ['required', 'string', 'max:255'],
            'execution_environment' => ['required', 'string', 'max:255'],
            'temperature' => ['nullable', 'numeric', 'between:0,2'],
            'top_p' => ['nullable', 'numeric', 'between:0,1'],
            'max_tokens' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'seed' => ['nullable', 'integer', 'min:0'],
            'parameters_json' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        return response()->json($project->modelConfigurations()->create($data + ['created_by' => $request->user()->id]), 201);
    }
}
