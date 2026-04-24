<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthApiController;
use Modules\Auth\Http\Controllers\Tenant\DashboardController;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('login', [AuthApiController::class, 'login'])->name('api.auth.login');
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('reset-password', [AuthApiController::class, 'resetPassword'])->name('api.auth.reset-password');
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

// Dashboard API routes
Route::prefix('dashboard')->middleware('auth:api')->group(function () {
    Route::get('stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
    Route::get('analytics', [DashboardController::class, 'getAnalytics'])->name('api.dashboard.analytics');
    Route::get('kpis', [DashboardController::class, 'getKPIs'])->name('api.dashboard.kpis');
});

// Activity Logs API routes
Route::prefix('activity-logs')->middleware('auth:api')->group(function () {
    Route::get('/', [\Modules\Auth\Http\Controllers\Api\ActivityLogApiController::class, 'index'])->name('api.activity-logs.index');
    Route::get('/{id}', [\Modules\Auth\Http\Controllers\Api\ActivityLogApiController::class, 'show'])->name('api.activity-logs.show');
    Route::get('/export', [\Modules\Auth\Http\Controllers\Api\ActivityLogApiController::class, 'export'])->name('api.activity-logs.export');
});

// Profile API routes
Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('/', [AuthApiController::class, 'getProfile'])->name('api.profile.get');
    Route::put('/', [AuthApiController::class, 'updateProfile'])->name('api.profile.update');
    Route::post('/avatar', [AuthApiController::class, 'uploadAvatar'])->name('api.profile.avatar');
    Route::delete('/avatar', [AuthApiController::class, 'removeAvatar'])->name('api.profile.avatar.remove');
    Route::post('/password', [AuthApiController::class, 'changePassword'])->name('api.profile.password');
    Route::get('/sessions', [AuthApiController::class, 'getSessions'])->name('api.profile.sessions');
    Route::post('/sessions/{id}/revoke', [AuthApiController::class, 'revokeSession'])->name('api.profile.sessions.revoke');
});
