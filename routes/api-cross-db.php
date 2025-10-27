<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CrossDb\LandlordController;
use App\Http\Controllers\Api\CrossDb\TenantController;

/*
|--------------------------------------------------------------------------
| Cross-Database API Routes
|--------------------------------------------------------------------------
|
| These routes handle communication between landlord and tenant databases
| with proper authentication and rate limiting.
|
*/

// Landlord API routes (accessible by tenants)
Route::prefix('cross-db/landlord')->group(function () {
    Route::get('modules', [LandlordController::class, 'getModules']);
    Route::get('modules/{id}', [LandlordController::class, 'getModule']);
    Route::post('modules/by-ids', [LandlordController::class, 'getModulesByIds']);
    Route::get('modules-stats', [LandlordController::class, 'getModuleStats']);
});

// Tenant API routes (accessible by landlord)
Route::prefix('cross-db/tenant/{tenant}')->group(function () {
    Route::get('brands', [TenantController::class, 'getBrands']);
    Route::get('brands/{id}', [TenantController::class, 'getBrand']);
    Route::get('brands/{brandId}/modules', [TenantController::class, 'getBrandModules']);
    Route::post('brands/{brandId}/assign-modules', [TenantController::class, 'assignBrandModules']);
    Route::get('brands-stats', [TenantController::class, 'getBrandStats']);
});
