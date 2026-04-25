<?php

use Illuminate\Support\Facades\Route;
use Modules\Geography\Http\Controllers\Api\CountryApiController;
use Modules\Geography\Http\Controllers\Api\ProvinceApiController;
use Modules\Geography\Http\Controllers\Api\CityApiController;
use Modules\Geography\Http\Controllers\Api\TownApiController;
use Modules\Geography\Http\Controllers\Api\StreetApiController;

// ─── Landlord Geography ─────────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('countries', CountryApiController::class);
    Route::apiResource('provinces', ProvinceApiController::class)->except(['show', 'update']);
    Route::apiResource('cities', CityApiController::class)->except(['show', 'update']);
    Route::apiResource('towns', TownApiController::class)->except(['show', 'update']);
    Route::apiResource('streets', StreetApiController::class)->except(['show', 'update']);
});
