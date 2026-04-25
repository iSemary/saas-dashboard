<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Api\BrandApiController;
use Modules\Customer\Http\Controllers\Api\BranchApiController;
use Modules\Customer\Http\Controllers\Api\OnboardingApiController;
use Modules\Customer\Http\Controllers\Api\Tenant\BrandApiController as TenantBrandApiController;
use Modules\Customer\Http\Controllers\Api\Tenant\BranchApiController as TenantBranchApiController;

// ─── Landlord Customer (Brands & Branches) ──────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('brands', BrandApiController::class);
    Route::apiResource('branches', BranchApiController::class);

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::post('create-brand', [OnboardingApiController::class, 'createBrand'])->name('create-brand');
    });
});

// ─── Tenant Brands & Branches ────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('brands', TenantBrandApiController::class);
    Route::apiResource('branches', TenantBranchApiController::class);
    Route::get('available-modules', [TenantBrandApiController::class, 'getAvailableModules'])->name('available-modules');
});
