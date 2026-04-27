<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookApiController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\BulkActionController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ImportSampleController;

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

// Generic Import routes
Route::middleware('auth:api')->prefix('import')->group(function () {
    Route::post('/{entity}/upload', [ImportController::class, 'upload'])->name('api.import.upload');
    Route::post('/{entity}/confirm', [ImportController::class, 'confirm'])->name('api.import.confirm');
    Route::get('/{entity}/template', [ImportController::class, 'template'])->name('api.import.template');
    Route::get('/jobs/{jobId}/status', [ImportController::class, 'status'])->name('api.import.status');
});

// Generic Bulk Action routes
Route::middleware('auth:api')->prefix('bulk-actions')->group(function () {
    Route::get('/{entity}/actions', [BulkActionController::class, 'actions'])->name('api.bulk-actions.list');
    Route::post('/{entity}/execute', [BulkActionController::class, 'execute'])->name('api.bulk-actions.execute');
    Route::post('/{entity}/preview', [BulkActionController::class, 'preview'])->name('api.bulk-actions.preview');
    Route::post('/{entity}/export', [BulkActionController::class, 'export'])->name('api.bulk-actions.export');
});

// Import Sample routes
Route::middleware('auth:api')->prefix('import-samples')->group(function () {
    Route::get('/', [ImportSampleController::class, 'listEntities'])->name('api.import-samples.list');
    Route::get('/{entity}/preview', [ImportSampleController::class, 'preview'])->name('api.import-samples.preview');
    Route::get('/{entity}/download-csv', [ImportSampleController::class, 'downloadCsv'])->name('api.import-samples.download-csv');
    Route::get('/{entity}/download-excel', [ImportSampleController::class, 'downloadExcel'])->name('api.import-samples.download-excel');
});

// Ticket routes
Route::middleware('auth:api')->prefix('tickets')->group(function () {
    Route::get('/kanban/list', [TicketController::class, 'kanban'])->name('api.tickets.kanban');
    Route::get('/{id}', [TicketController::class, 'show'])->name('api.tickets.show');
    Route::post('/{id}/comments', [TicketController::class, 'addComment'])->name('api.tickets.comment');
    Route::put('/{id}/assign', [TicketController::class, 'assign'])->name('api.tickets.assign');
    Route::put('/{id}/status', [TicketController::class, 'changeStatus'])->name('api.tickets.status');
    Route::put('/{id}/priority', [TicketController::class, 'changePriority'])->name('api.tickets.priority');
    Route::put('/{id}/close', [TicketController::class, 'close'])->name('api.tickets.close');
    Route::put('/{id}/reopen', [TicketController::class, 'reopen'])->name('api.tickets.reopen');
});
