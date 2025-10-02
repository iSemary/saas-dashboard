<?php

use Illuminate\Support\Facades\Route;
use Modules\Monitoring\Http\Controllers\MonitoringController;

/*
|--------------------------------------------------------------------------
| Monitoring Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Landlord Monitoring Dashboard system.
| These routes provide system health monitoring, tenant behavior analysis,
| error management, resource insights, and developer tools.
|
*/

Route::prefix('landlord/monitoring')->name('landlord.monitoring.')->middleware(['auth:web', 'role:landlord|developer', '2fa'])->group(function () {
    
    // Main monitoring dashboard
    Route::get('/', [MonitoringController::class, 'index'])->name('index');
    
    // System Health
    Route::get('/system-health', [MonitoringController::class, 'systemHealth'])->name('system-health');
    Route::get('/api/system-health', [MonitoringController::class, 'getSystemHealthData'])->name('api.system-health');
    
    // Tenant Behavior
    Route::get('/tenant-behavior', [MonitoringController::class, 'tenantBehavior'])->name('tenant-behavior');
    Route::get('/api/tenant-behavior', [MonitoringController::class, 'getTenantBehaviorData'])->name('api.tenant-behavior');
    
    // Error Management
    Route::get('/error-management', [MonitoringController::class, 'errorManagement'])->name('error-management');
    Route::get('/api/errors', [MonitoringController::class, 'getErrorData'])->name('api.errors');
    
    // Resource Insights
    Route::get('/resource-insights', [MonitoringController::class, 'resourceInsights'])->name('resource-insights');
    Route::get('/api/resources', [MonitoringController::class, 'getResourceData'])->name('api.resources');
    
    // Admin Tools
    Route::get('/admin-tools', [MonitoringController::class, 'adminTools'])->name('admin-tools');
    Route::post('/admin-tools/consistency-check', [MonitoringController::class, 'runConsistencyCheck'])->name('admin-tools.consistency-check');
    
    // Developer Tools
    Route::get('/developer-tools', [MonitoringController::class, 'developerTools'])->name('developer-tools');
    Route::get('/api/migration-status', [MonitoringController::class, 'getMigrationStatus'])->name('api.migration-status');
    
    // Tenant-specific monitoring
    Route::get('/tenant/{id}', [MonitoringController::class, 'tenantMonitoring'])->name('tenant');
    
});
