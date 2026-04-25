<?php

use Illuminate\Support\Facades\Route;
use Modules\Localization\Http\Controllers\Api\LanguageApiController;
use Modules\Localization\Http\Controllers\Api\TranslationApiController;

// ─── Landlord Localization ─────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('languages', LanguageApiController::class);
    Route::apiResource('translations', TranslationApiController::class);
});

// ─── Public Translations ─────────────────────────────────────────
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('translations', [TranslationApiController::class, 'index']);
});
