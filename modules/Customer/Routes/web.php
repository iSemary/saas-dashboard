<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\BrandController;
use Modules\Customer\Http\Controllers\BrandWebController;
use Modules\Customer\Http\Controllers\BranchController;
use Modules\Customer\Http\Controllers\Landlord\BranchController as LandlordBranchController;
use Modules\Customer\Http\Controllers\Tenant\BrandController as TenantBrandController;

/*
|--------------------------------------------------------------------------
| Customer Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Customer module including brands and branches.
| These routes provide CRUD operations for customer-related entities.
|
*/

// Landlord routes (for landlord users)
Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    
    // Brand API routes
    Route::middleware('can:read.brands')->group(function () {
        Route::resource('brands', BrandController::class);
        Route::get('brands/slug/{slug}', [BrandController::class, 'showBySlug'])->name('brands.show-by-slug');
        Route::get('brands/tenant/{tenantId}', [BrandController::class, 'getByTenant'])->name('brands.by-tenant');
        Route::get('brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::get('brands/stats', [BrandController::class, 'stats'])->name('brands.stats');
    });

    // Brand Web Interface routes
    Route::middleware('can:read.brands')->group(function () {
        Route::resource('brands-web', BrandWebController::class)->names('brands-web');
    });

    // Branch routes for landlord
    Route::middleware('can:read.branches')->group(function () {
        Route::resource('branches', LandlordBranchController::class)->names('branches');
        Route::get('branches/brand/{brandId}', [LandlordBranchController::class, 'getByBrand'])->name('branches.by-brand');
        Route::get('branches/search', [LandlordBranchController::class, 'search'])->name('branches.search');
        Route::get('branches/stats', [LandlordBranchController::class, 'stats'])->name('branches.stats');
        Route::get('branches/active', [LandlordBranchController::class, 'getActiveBranches'])->name('branches.active');
        Route::get('branches/location', [LandlordBranchController::class, 'getByLocation'])->name('branches.location');
        
        // Import routes
        Route::get('branches/import', [LandlordBranchController::class, 'import'])->name('branches.import');
        Route::post('branches/import', [LandlordBranchController::class, 'processImport'])->name('branches.process-import');
        Route::get('branches/template', [LandlordBranchController::class, 'downloadTemplate'])->name('branches.template');
    });
});

// Tenant routes (for tenant users)
Route::prefix('tenant')->name('tenant.')->middleware(['auth:web', 'tenant', '2fa'])->group(function () {
    
    // Brand routes (read-only for tenants)
    Route::middleware('can:read.brands')->group(function () {
        Route::get('brands', [TenantBrandController::class, 'index'])->name('brands.index');
        Route::get('brands/{id}', [TenantBrandController::class, 'show'])->name('brands.show');
        Route::get('brands/search', [TenantBrandController::class, 'search'])->name('brands.search');
        Route::get('brands/stats', [TenantBrandController::class, 'stats'])->name('brands.stats');
    });
    
    // Branch routes
    Route::middleware('can:read.branches')->group(function () {
        Route::resource('branches', BranchController::class)->names('branches');
        Route::get('branches/brand/{brandId}', [BranchController::class, 'getByBrand'])->name('branches.by-brand');
        Route::get('branches/search', [BranchController::class, 'search'])->name('branches.search');
        Route::get('branches/stats', [BranchController::class, 'stats'])->name('branches.stats');
        Route::get('branches/active', [BranchController::class, 'getActiveBranches'])->name('branches.active');
        Route::get('branches/location', [BranchController::class, 'getByLocation'])->name('branches.location');
        
        // Import routes
        Route::get('branches/import', [BranchController::class, 'import'])->name('branches.import');
        Route::post('branches/import', [BranchController::class, 'processImport'])->name('branches.process-import');
        Route::get('branches/template', [BranchController::class, 'downloadTemplate'])->name('branches.template');
    });
});