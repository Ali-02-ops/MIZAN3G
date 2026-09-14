<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CulturalTermController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\GenerationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'abilities:mizan3g:api'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::post('/projects/{project}/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::post('/documents/{document}/versions', [DocumentController::class, 'storeVersion']);
        Route::get('/documents/{document}/versions/{version}', [DocumentController::class, 'showVersion']);

        Route::get('/document-versions/{version}/cultural-terms', [CulturalTermController::class, 'index']);
        Route::post('/document-versions/{version}/cultural-terms', [CulturalTermController::class, 'store']);
        Route::patch('/cultural-terms/{term}', [CulturalTermController::class, 'update']);
        Route::post('/audits/{audit}/generations/import', [GenerationController::class, 'import']);
    });
});
