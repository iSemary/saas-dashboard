<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Guest\AuthController;
use Modules\Auth\Http\Controllers\PermissionController;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\Landlord\UserController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('permissions', PermissionController::class)->names('permissions');
    Route::resource('roles', RoleController::class)->names('roles');

    // Show attempts per user
    Route::get('attempts/{id}', [AuthController::class, 'showAttempts'])->name('attempts.index');

    // Profile Page [Show and Update]
    Route::get('profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
});
