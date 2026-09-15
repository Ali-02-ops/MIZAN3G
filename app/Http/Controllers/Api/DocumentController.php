<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SourceDocument;
use App\Models\SourceDocumentVersion;
use App\Services\Documents\DocumentVersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project->documents()
            ->with('versions')
            ->latest()
            ->get());
    }

    public function store(Request $request, Project $project, DocumentVersionService $service): JsonResponse
    {
        $this->authorize('create', [SourceDocument::class, $project]);
        $attributes = $request->validate(['title' => ['required', 'string', 'max:255'], 'source_language' => ['required', 'string', 'max:16'], 'text_content' => ['required', 'string', 'min:1'], 'author' => ['nullable', 'string', 'max:255'], 'publication' => ['nullable', 'string', 'max:255'], 'publication_year' => ['nullable', 'integer', 'between:1,9999'], 'file_path' => ['nullable', 'string', 'max:1024'], 'notes' => ['nullable', 'string', 'max:10000']]);
        $document = $service->createDocument($project, $request->user(), $attributes);

        return response()->json($document->load('versions'), 201);
    }

    public function show(SourceDocument $document): JsonResponse
    {
        $this->authorize('view', $document);

        return response()->json($document->load('versions'));
    }

    public function storeVersion(Request $request, SourceDocument $document, DocumentVersionService $service): JsonResponse
    {
        $this->authorize('update', $document);
        $attributes = $request->validate(['text_content' => ['required', 'string', 'min:1'], 'file_path' => ['nullable', 'string', 'max:1024'], 'notes' => ['nullable', 'string', 'max:10000']]);
        $version = $service->createVersion($document, $request->user(), $attributes['text_content'], $attributes['file_path'] ?? null, $attributes['notes'] ?? null);

        return response()->json($version, 201);
    }

    public function showVersion(SourceDocument $document, SourceDocumentVersion $version): JsonResponse
    {
        abort_unless($version->source_document_id === $document->getKey(), 404);
        $this->authorize('view', $document);

        return response()->json($version);
    }
}
