<?php

use Illuminate\Support\Facades\Route;
use Modules\FileManager\Http\Controllers\Api\DocumentApiController;
use Modules\FileManager\Http\Controllers\Api\FolderApiController;

/*
 *--------------------------------------------------------------------------
 * File Manager API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for the File Manager module.
 *
 */

Route::middleware('auth:api')->prefix('documents')->group(function () {
    // File routes
    Route::get('/', [DocumentApiController::class, 'index'])->name('api.documents.index');
    Route::post('/upload', [DocumentApiController::class, 'upload'])->name('api.documents.upload');
    Route::get('/{id}', [DocumentApiController::class, 'show'])->name('api.documents.show');
    Route::put('/{id}', [DocumentApiController::class, 'update'])->name('api.documents.update');
    Route::delete('/{id}', [DocumentApiController::class, 'destroy'])->name('api.documents.destroy');
    Route::get('/{id}/download', [DocumentApiController::class, 'download'])->name('api.documents.download');
    Route::get('/{id}/versions', [DocumentApiController::class, 'versions'])->name('api.documents.versions');
    Route::post('/bulk-delete', [DocumentApiController::class, 'bulkDelete'])->name('api.documents.bulk-delete');

    // Folder routes
    Route::prefix('folders')->group(function () {
        Route::get('/', [FolderApiController::class, 'index'])->name('api.documents.folders.index');
        Route::post('/', [FolderApiController::class, 'store'])->name('api.documents.folders.store');
        Route::get('/{id}', [FolderApiController::class, 'show'])->name('api.documents.folders.show');
        Route::put('/{id}', [FolderApiController::class, 'update'])->name('api.documents.folders.update');
        Route::delete('/{id}', [FolderApiController::class, 'destroy'])->name('api.documents.folders.destroy');
    });
});