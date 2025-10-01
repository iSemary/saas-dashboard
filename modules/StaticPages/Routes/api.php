<?php

use Illuminate\Support\Facades\Route;
use Modules\StaticPages\Http\Controllers\StaticPagesController;
use Modules\StaticPages\Http\Controllers\Api\StaticPageApiController;

/*
 *--------------------------------------------------------------------------
 * Static Pages API Routes
 *--------------------------------------------------------------------------
 *
 * API routes for static pages with multi-language support
 *
*/

// Public API routes (no authentication required)
Route::prefix('static-pages')->group(function () {
    Route::get('/', [StaticPageApiController::class, 'index']);
    Route::get('/languages', [StaticPageApiController::class, 'languages']);
    Route::get('/search', [StaticPageApiController::class, 'search']);
    Route::get('/type/{type}', [StaticPageApiController::class, 'getByType']);
    Route::get('/{slug}', [StaticPageApiController::class, 'show']);
});

// Admin API routes (authentication required)
Route::middleware(['auth:sanctum', 'tenant'])->prefix('admin/static-pages')->group(function () {
    Route::apiResource('pages', StaticPagesController::class)->names('admin.staticpages');
});
