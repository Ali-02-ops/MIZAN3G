<?php

use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CulturalTermController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DriftTypeController;
use App\Http\Controllers\Api\ExpertAssignmentController;
use App\Http\Controllers\Api\GenerationController;
use App\Http\Controllers\Api\ModelConfigurationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReproducibilityController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\Api\TaxonomyController;
use App\Http\Controllers\Api\TermExtractionController;
use App\Http\Controllers\Api\TermOutputController;
use App\Http\Controllers\Api\WorkstationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'mizan-api'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/organisations/{organisation}/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}/audits', [AuditController::class, 'index']);
        Route::get('/projects/{project}/audit-logs', [AuditLogController::class, 'index']);
        Route::post('/projects/{project}/audits', [AuditController::class, 'store']);
        Route::get('/projects/{project}/model-configurations', [ModelConfigurationController::class, 'index']);
        Route::post('/projects/{project}/model-configurations', [ModelConfigurationController::class, 'store']);
        Route::get('/projects/{project}/documents', [DocumentController::class, 'index']);
        Route::get('/taxonomy/cultural-categories', [TaxonomyController::class, 'categories']);
        Route::get('/taxonomy/drift-types', [DriftTypeController::class, 'index']);
        Route::get('/prompt-versions', [PromptController::class, 'index']);
        Route::get('/audits/{audit}', [AuditController::class, 'show']);
        Route::post('/audits/{audit}/freeze', [AuditController::class, 'freeze']);
        Route::get('/audits/{audit}/generations', [GenerationController::class, 'index']);
        Route::post('/audits/{audit}/generations', [GenerationController::class, 'queue']);
        Route::post('/audits/{audit}/expert-assignments', [ExpertAssignmentController::class, 'store']);
        Route::get('/expert-assignments', [ExpertAssignmentController::class, 'index']);
        Route::get('/generations/{generation}/term-outputs', [TermOutputController::class, 'index']);
        Route::post('/generations/{generation}/term-outputs', [TermOutputController::class, 'store']);
        Route::patch('/term-outputs/{termOutput}', [TermOutputController::class, 'update']);
        Route::put('/term-outputs/{termOutput}/researcher-rating', [RatingController::class, 'upsertResearcher']);
        Route::put('/term-outputs/{termOutput}/expert-rating', [RatingController::class, 'upsertExpert']);
        Route::get('/audits/{audit}/scores', [ScoreController::class, 'index']);
        Route::post('/audits/{audit}/scores', [ScoreController::class, 'snapshot']);
        Route::get('/audits/{audit}/reproducibility-package', [ReproducibilityController::class, 'show']);
        Route::get('/audits/{audit}/report.pdf', [ReportController::class, 'download']);

        Route::post('/projects/{project}/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::post('/documents/{document}/versions', [DocumentController::class, 'storeVersion']);
        Route::get('/documents/{document}/versions/{version}', [DocumentController::class, 'showVersion']);

        Route::get('/document-versions/{version}/cultural-terms', [CulturalTermController::class, 'index']);
        Route::post('/document-versions/{version}/cultural-terms', [CulturalTermController::class, 'store']);
        Route::post('/document-versions/{version}/analyze-cultural-terms', [TermExtractionController::class, 'store']);
        Route::post('/workstation/analyze', [WorkstationController::class, 'analyze'])->middleware('throttle:30,1');
        Route::patch('/cultural-terms/{term}', [CulturalTermController::class, 'update']);
        Route::post('/audits/{audit}/generations/import', [GenerationController::class, 'import']);
    });
});
