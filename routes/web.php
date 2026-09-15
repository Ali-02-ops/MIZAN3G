<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth')->name('dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/{screen}', [WorkspaceController::class, 'show'])
    ->whereIn('screen', ['projects', 'documents', 'inventory', 'workstation', 'audits', 'expert-reviews', 'results', 'reports', 'settings'])
    ->middleware('auth')
    ->name('workspace.screen');
