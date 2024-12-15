<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Guest\AuthController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('permissions', AuthController::class)->names('permissions');
    Route::resource('roles', AuthController::class)->names('roles');
});
