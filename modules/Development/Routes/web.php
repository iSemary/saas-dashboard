<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\ConfigurationController;
use Modules\Development\Http\Controllers\FlowController;
use Modules\Development\Http\Controllers\IpBlacklistController;
use Modules\Development\Http\Controllers\BackupController;
use Modules\Development\Http\Controllers\CodeBuilderController;
use Modules\Development\Http\Controllers\AnalysisController;
use Modules\Development\Http\Controllers\EntityController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        // App Configurations
        Route::resource('configurations', ConfigurationController::class)->names('configurations');

        // Backup Monitor
        Route::get('backups', [BackupController::class, "index"])->name('backups.index');

        // IP Black List
        Route::resource('ip-blacklists', IpBlacklistController::class)->names('ip-blacklists');

        // Flows 
        Route::get('flows/modules', [FlowController::class, "modules"])->name('flows.modules');
        Route::get('flows/database', [FlowController::class, "database"])->name('flows.database');

        // Entities and module
        Route::get('entities', [EntityController::class, "index"])->name('entities.index');
        Route::post('entities/sync', [EntityController::class, "sync"])->name('entities.sync');
        Route::post('entities/store', [EntityController::class, "store"])->name('entities.store');

        // Development Builder
        Route::get("code-builder", [CodeBuilderController::class, "show"])->name("code-builder.show");
        Route::post("code-builder", [CodeBuilderController::class, "submit"])->name("code-builder.submit");

        Route::get("env-diff", [AnalysisController::class, "showEnvDiff"])->name("env-diff.show");

        Route::get("system-status", [AnalysisController::class, "showSystemStatus"])->name("system-status.show");

        Route::get('system-status/check', [AnalysisController::class, 'checkSystemStatus'])->name('system_status.check');
        Route::post('system-status/check-service', [AnalysisController::class, 'checkIndividualService'])->name('system_status.check_service');
        Route::post('system-status/clear-cache', [AnalysisController::class, 'clearCache'])->name('system_status.clear_cache');
        Route::post('system-status/restart-queue', [AnalysisController::class, 'restartQueue'])->name('system_status.restart_queue');
        Route::post('system-status/restart-supervisor', [AnalysisController::class, 'restartSupervisor'])->name('system_status.restart_supervisor');
    });
});
