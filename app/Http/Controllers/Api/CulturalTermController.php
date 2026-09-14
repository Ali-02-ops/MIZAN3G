<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CulturalTerm;
use App\Models\SourceDocumentVersion;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CulturalTermController extends Controller
{
    public function index(SourceDocumentVersion $version): JsonResponse
    {
        $this->authorize('view', $version->document->project);

        return response()->json($version->terms()->with(['category', 'subcategory'])->get());
    }

    public function store(Request $request, SourceDocumentVersion $version): JsonResponse
    {
        $this->authorize('update', $version->document->project);

        $term = $version->terms()->create($this->validatedAttributes($request) + ['created_by' => $request->user()->getKey()]);

        return response()->json($term->load(['category', 'subcategory']), 201);
    }

    public function update(Request $request, CulturalTerm $term): JsonResponse
    {
        $this->authorize('update', $term->documentVersion->document->project);

        $term->update($this->validatedAttributes($request, false));

        return response()->json($term->fresh()->load(['category', 'subcategory']));
    }

    /** @return array<string, mixed> */
    private function validatedAttributes(Request $request, bool $creating = true): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'source_phrase' => [$required, 'string', 'max:1000'],
            'source_sentence' => ['nullable', 'string', 'max:10000'],
            'source_context' => ['nullable', 'string', 'max:50000'],
            'start_offset' => ['nullable', 'integer', 'min:0'],
            'end_offset' => ['nullable', 'integer', 'gte:start_offset'],
            'category_id' => [$required, Rule::exists('mizan3g_cultural_categories', 'id')->where('active', true)],
            'subcategory_id' => ['nullable', Rule::exists('mizan3g_cultural_subcategories', 'id')->where(fn (Builder $query) => $query->where('category_id', $request->input('category_id')))->where('active', true)],
            'cultural_significance' => ['nullable', 'string', 'max:10000'],
            'selected_for_audit' => ['sometimes', 'boolean'],
            'selection_reason' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
