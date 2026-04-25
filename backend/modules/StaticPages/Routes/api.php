<?php

use Illuminate\Support\Facades\Route;
use Modules\StaticPages\Http\Controllers\Api\StaticPageApiController;

// ─── Landlord Static Pages ──────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('static-pages', StaticPageApiController::class);
});

// ─── Public Static Pages ─────────────────────────────────────────
Route::prefix('static-pages')->group(function () {
    Route::get('/', [StaticPageApiController::class, 'index']);
    Route::get('/languages', [StaticPageApiController::class, 'languages']);
    Route::get('/search', [StaticPageApiController::class, 'search']);
    Route::get('/type/{type}', [StaticPageApiController::class, 'getByType']);
    Route::get('/{slug}', [StaticPageApiController::class, 'show']);
});
