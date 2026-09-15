<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriftType;
use Illuminate\Http\JsonResponse;

class DriftTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(DriftType::query()->where('active', true)->orderBy('name')->get());
    }
}
