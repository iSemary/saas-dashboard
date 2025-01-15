<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\TenantController;
use Modules\Tenant\Http\Controllers\SystemUserController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('tenants', TenantController::class)->names('tenants');

    Route::resource('system-users', SystemUserController::class)->names('system-users');

    Route::resource('clients', TenantController::class)->names('clients');
});
