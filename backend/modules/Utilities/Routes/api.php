<?php

use Illuminate\Support\Facades\Route;
use Modules\Utilities\Http\Controllers\Api\CategoryApiController;
use Modules\Utilities\Http\Controllers\Api\TagApiController;
use Modules\Utilities\Http\Controllers\Api\TypeApiController;
use Modules\Utilities\Http\Controllers\Api\IndustryApiController;
use Modules\Utilities\Http\Controllers\Api\CurrencyApiController;
use Modules\Utilities\Http\Controllers\Api\UnitApiController;
use Modules\Utilities\Http\Controllers\Api\AnnouncementApiController;
use Modules\Utilities\Http\Controllers\Api\ReleaseApiController;
use Modules\Utilities\Http\Controllers\Api\ModuleApiController;

// ─── Landlord Utilities ─────────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('categories', CategoryApiController::class);
    Route::apiResource('tags', TagApiController::class)->except(['show', 'update']);
    Route::apiResource('types', TypeApiController::class)->except(['show']);
    Route::apiResource('industries', IndustryApiController::class)->except(['show']);
    Route::apiResource('currencies', CurrencyApiController::class);
    Route::apiResource('units', UnitApiController::class)->except(['show', 'update']);
    Route::apiResource('announcements', AnnouncementApiController::class);
    Route::apiResource('releases', ReleaseApiController::class)->except(['show', 'update']);

    // Modules
    Route::get('modules', [ModuleApiController::class, 'index'])->name('modules.index');
    Route::post('modules', [ModuleApiController::class, 'store'])->name('modules.store');
    Route::get('modules/{id}', [ModuleApiController::class, 'show'])->name('modules.show');
    Route::put('modules/{id}', [ModuleApiController::class, 'update'])->name('modules.update');
    Route::patch('modules/{id}', [ModuleApiController::class, 'toggle'])->name('modules.toggle');
});
