<?php

use Illuminate\Support\Facades\Route;
use Modules\Monitoring\Http\Controllers\Api\MonitoringApiController;

Route::prefix('landlord')
    ->name('landlord.monitoring.api.')
    ->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])
    ->group(function () {
        Route::get('monitoring', [MonitoringApiController::class, 'overview'])->name('overview');
        Route::get('system-health', [MonitoringApiController::class, 'systemHealth'])->name('system-health');
        Route::get('tenant-monitoring', [MonitoringApiController::class, 'tenantMonitoring'])->name('tenant-monitoring');
    });
