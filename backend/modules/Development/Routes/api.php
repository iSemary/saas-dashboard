<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\Api\DatabaseController;
use Modules\Development\Http\Controllers\Api\DevelopmentController;
use Modules\Development\Http\Controllers\Api\SystemStatusApiController;
use Modules\Development\Http\Controllers\Api\ConfigurationApiController;
use Modules\Development\Http\Controllers\Api\BackupApiController;
use Modules\Development\Http\Controllers\Api\FeatureFlagApiController;
use Modules\Development\Http\Controllers\Api\IpBlacklistApiController;
use Modules\Development\Http\Controllers\Api\CodeBuilderApiController;
use Modules\Development\Http\Controllers\Api\EnvDiffApiController;
use Modules\Development\Http\Controllers\Api\ModuleEntityApiController;
use Modules\Development\Http\Controllers\Api\MonitoringApiController;
use Modules\Development\Http\Controllers\Api\SystemHealthApiController;
use Modules\Development\Http\Controllers\Api\TenantMonitoringApiController;
use Modules\Development\Http\Controllers\Api\DocumentationApiController;

// ─── Landlord Development ───────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::get('system-status', [SystemStatusApiController::class, 'index'])->name('system-status.index');
    Route::apiResource('configurations', ConfigurationApiController::class);
    Route::apiResource('backups', BackupApiController::class)->except(['show', 'update']);
    Route::get('backups/{id}/download', [BackupApiController::class, 'download'])->name('backups.download');
    Route::apiResource('feature-flags', FeatureFlagApiController::class);
    Route::apiResource('ip-blacklists', IpBlacklistApiController::class);
    Route::post('code-builder', [CodeBuilderApiController::class, 'build'])->name('code-builder.build');
    Route::get('env-diff', [EnvDiffApiController::class, 'index'])->name('env-diff.index');
    Route::get('entities', [ModuleEntityApiController::class, 'index'])->name('entities.index');
    Route::post('module-entities', [ModuleEntityApiController::class, 'sync'])->name('entities.sync');
    Route::get('module-entities-map', [ModuleEntityApiController::class, 'map'])->name('entities.map');
    Route::get('monitoring', [MonitoringApiController::class, 'index'])->name('monitoring.index');
    Route::get('system-health', [SystemHealthApiController::class, 'index'])->name('system-health.index');
    Route::get('tenant-monitoring', [TenantMonitoringApiController::class, 'index'])->name('tenant-monitoring.index');
    Route::apiResource('documentation', DocumentationApiController::class);
});

// ─── Public Feature Flags Evaluate ──────────────────────────────
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::match(['get', 'post'], 'feature-flags/evaluate', [FeatureFlagApiController::class, 'evaluate']);
});

// ─── Development Utility Routes ─────────────────────────────────
Route::prefix('development')->group(function () {
    Route::get('routes', [DevelopmentController::class, "routes"]);
    Route::get('databases/flow', [DatabaseController::class, "index"]);
    Route::post('databases/flow', [DatabaseController::class, "syncFlow"]);
});
