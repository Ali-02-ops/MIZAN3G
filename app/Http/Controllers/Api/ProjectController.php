<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(Project::query()
            ->whereIn('organisation_id', $request->user()->organisations()->select('mizan3g_organisations.id'))
            ->with('organisation:id,name')
            ->withCount('documents')
            ->latest()
            ->get());
    }

    public function store(Request $request, Organisation $organisation): JsonResponse
    {
        $this->authorize('create', [Project::class, $organisation]);

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'objective' => ['nullable', 'string', 'max:10000'],
            'source_language' => ['required', 'string', 'max:16'],
            'target_language' => ['required', 'string', 'max:16'],
        ]);

        $baseSlug = Str::slug($attributes['name']) ?: 'project';
        $slug = $baseSlug;
        $suffix = 2;
        while ($organisation->projects()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        $project = $organisation->projects()->create($attributes + [
            'slug' => $slug,
            'created_by' => $request->user()->getKey(),
        ]);

        return response()->json($project->load('organisation:id,name'), 201);
    }
}
