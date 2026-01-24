<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthApiController;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('login', [AuthApiController::class, 'login'])->name('api.auth.login');
    Route::post('register', [AuthApiController::class, 'register'])->name('api.auth.register');
    Route::post('2fa/verify', [AuthApiController::class, 'verify2FA'])->name('api.auth.2fa.verify');
    Route::post('2fa/setup', [AuthApiController::class, 'setup2FA'])->name('api.auth.2fa.setup');
    Route::post('2fa/confirm', [AuthApiController::class, 'confirm2FA'])->name('api.auth.2fa.confirm');
    Route::post('2fa/disable', [AuthApiController::class, 'disable2FA'])->name('api.auth.2fa.disable');
    Route::get('2fa/recovery-codes', [AuthApiController::class, 'getRecoveryCodes'])->name('api.auth.2fa.recovery-codes');

    // Protected routes - using Passport API guard
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthApiController::class, 'me'])->name('api.auth.me');
        Route::post('logout', [AuthApiController::class, 'logout'])->name('api.auth.logout');
    });
});
