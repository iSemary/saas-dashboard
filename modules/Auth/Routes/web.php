<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\PermissionController;
use Modules\Auth\Http\Controllers\RoleController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('permissions', PermissionController::class)->names('permissions');
    Route::resource('roles', RoleController::class)->names('roles');
});
