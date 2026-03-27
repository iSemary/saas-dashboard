<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->prefix('webhooks')->group(function () {
    Route::get('/', [WebhookApiController::class, 'index'])->name('api.webhooks.index');
    Route::post('/', [WebhookApiController::class, 'store'])->name('api.webhooks.store');
    Route::get('/{id}', [WebhookApiController::class, 'show'])->name('api.webhooks.show');
    Route::put('/{id}', [WebhookApiController::class, 'update'])->name('api.webhooks.update');
    Route::delete('/{id}', [WebhookApiController::class, 'destroy'])->name('api.webhooks.destroy');
    Route::post('/{id}/test', [WebhookApiController::class, 'test'])->name('api.webhooks.test');
    Route::get('/{id}/logs', [WebhookApiController::class, 'logs'])->name('api.webhooks.logs');
});

// Reports routes
Route::middleware('auth:api')->prefix('reports')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ReportApiController::class, 'index'])->name('api.reports.index');
});

// Import/Export routes
Route::middleware('auth:api')->prefix('import-export')->group(function () {
    Route::get('/export', [\App\Http\Controllers\Api\ImportExportApiController::class, 'export'])->name('api.import-export.export');
    Route::post('/import', [\App\Http\Controllers\Api\ImportExportApiController::class, 'import'])->name('api.import-export.import');
    Route::get('/history', [\App\Http\Controllers\Api\ImportExportApiController::class, 'importHistory'])->name('api.import-export.history');
});

// Backup/Restore routes
Route::middleware('auth:api')->prefix('backups')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\BackupApiController::class, 'index'])->name('api.backups.index');
    Route::post('/', [\App\Http\Controllers\Api\BackupApiController::class, 'create'])->name('api.backups.create');
    Route::get('/{filename}/download', [\App\Http\Controllers\Api\BackupApiController::class, 'download'])->name('api.backups.download');
    Route::post('/{filename}/restore', [\App\Http\Controllers\Api\BackupApiController::class, 'restore'])->name('api.backups.restore');
    Route::delete('/{filename}', [\App\Http\Controllers\Api\BackupApiController::class, 'destroy'])->name('api.backups.destroy');
});
