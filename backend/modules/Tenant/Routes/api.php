<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\Api\TenantApiController;
use Modules\Tenant\Http\Controllers\Api\TenantDashboardApiController;
use Modules\Tenant\Http\Controllers\Api\TenantModuleApiController;
use Modules\Tenant\Http\Controllers\Api\TenantOwnerApiController;

// ─── Landlord Tenant Management ─────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('tenants', TenantApiController::class);
    Route::apiResource('tenant-owners', TenantOwnerApiController::class);
});

// ─── Tenant Modules ──────────────────────────────────────────────
Route::prefix('tenant/modules')->name('tenant.modules.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('/', [TenantModuleApiController::class, 'index'])->name('index');
    Route::get('/{moduleKey}', [TenantModuleApiController::class, 'show'])->name('show');
});

// ─── Tenant Dashboard ────────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('dashboard/stats', [TenantDashboardApiController::class, 'stats'])->name('dashboard.stats');
});
