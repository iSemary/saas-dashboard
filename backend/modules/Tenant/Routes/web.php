<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;
use Modules\Tenant\Http\Controllers\SystemUserController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord|developer', '2fa'])->group(function () {
    Route::resource('tenants', TenantController::class)->names('tenants');

    // Tenant database management routes
    Route::post('tenants/{id}/remigrate', [TenantController::class, 'reMigrate'])->name('tenants.remigrate');
    Route::post('tenants/{id}/seed', [TenantController::class, 'seedDatabase'])->name('tenants.seed');
    Route::post('tenants/{id}/reseed', [TenantController::class, 'reSeedDatabase'])->name('tenants.reseed');
    Route::get('tenants/{id}/health', [TenantController::class, 'getDatabaseHealth'])->name('tenants.health');

    Route::resource('system-users', SystemUserController::class)->names('system-users');
    Route::post('system-users/check-email', [SystemUserController::class, 'checkEmail'])->name('system-users.check-email');
});
