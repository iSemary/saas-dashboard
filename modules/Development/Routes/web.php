<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\ConfigurationController;
use Modules\Development\Http\Controllers\FlowController;
use Modules\Development\Http\Controllers\IpBlacklistController;
use Modules\Utilities\Http\Controllers\CodeBuilderController;
use Modules\Utilities\Http\Controllers\AnalysisController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        Route::resource('configurations', ConfigurationController::class)->names('configurations');

        Route::resource('ip-blacklists', IpBlacklistController::class)->names('ip-blacklists');

        // Flows 
        Route::get('flows/modules', [FlowController::class, "modules"])->name('flows.modules');
        Route::get('flows/database', [FlowController::class, "database"])->name('flows.database');

        Route::get("code-builder", [CodeBuilderController::class, "show"])->name("code-builder.show");
        Route::post("code-builder", [CodeBuilderController::class, "submit"])->name("code-builder.submit");
        Route::get("env-diff", [AnalysisController::class, "showEnvDiff"])->name("env-diff.show");
    });
});
