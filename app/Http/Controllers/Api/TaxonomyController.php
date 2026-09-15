<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CulturalCategory;
use Illuminate\Http\JsonResponse;

class TaxonomyController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json(CulturalCategory::query()
            ->where('active', true)
            ->with(['subcategories' => fn ($query) => $query->where('active', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->get());
    }
}
