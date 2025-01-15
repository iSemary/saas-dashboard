<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('tenants', TenantController::class)->names('tenants');

    Route::resource('users', TenantController::class)->names('users');

    Route::resource('clients', TenantController::class)->names('clients');
});
