<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Guest\AuthController;
use Modules\Auth\Http\Controllers\PermissionController;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\Landlord\UserController;
use Modules\Auth\Http\Controllers\Guest\OrganizationController;
use Modules\Auth\Http\Controllers\Guest\PasswordController;
use Modules\Auth\Http\Controllers\Guest\TwoFactorAuthController;
use Modules\Auth\Http\Controllers\Guest\ActivityLogController;
use Modules\Auth\Http\Controllers\Landlord\DashboardController;
use App\Http\Controllers\TenantController;

// Routes for guests (no auth middleware)
Route::middleware('guest')->group(function () {
    // Check Organization
    Route::post('organization/check', [OrganizationController::class, 'check'])->name('organization.check');

    // Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Forget Password
    Route::get('password/forget', [PasswordController::class, 'showForgetForm'])->name('password.forget.show');
    Route::post('password/forget', [PasswordController::class, 'submitForgetForm'])->name('password.forget.submit');

    // Reset Password
    Route::get('password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset.show');
    Route::post('password/reset', [PasswordController::class, 'submitResetForm'])->name('password.reset.submit');
});

// Routes for authenticated users (auth middleware)
Route::middleware('auth')->group(function () {
    Route::middleware('2fa')->group(function () {
        Route::get('/', [TenantController::class, "index"])->name('home');
        
        // Tenant Dashboard API Routes
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('stats', [\Modules\Auth\Http\Controllers\Tenant\DashboardController::class, 'getStats'])->name('stats');
        });
    });

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Lock
    Route::middleware('2fa')->group(function () {
        Route::get('lock', [PasswordController::class, 'showLock'])->name('lock.show');
        Route::post('lock', [PasswordController::class, 'lock'])->name('lock.submit');
        Route::post('unlock', [PasswordController::class, 'unlock'])->name('unlock.submit');
    });

    // 2FA
    Route::get('2fa/setup', [TwoFactorAuthController::class, 'showSetupForm'])->name('2fa.setup');
    Route::get('2fa/validate', [TwoFactorAuthController::class, 'showValidateForm'])->name('2fa.validate');
    Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('2fa/check', [TwoFactorAuthController::class, 'check'])->name('2fa.check');

    Route::middleware('2fa')->group(function () {
        Route::post('/2fa/reset', [TwoFactorAuthController::class, "reset"])->name('2fa.reset');
    });



    // Login Attempts
    Route::get('attempts', [TwoFactorAuthController::class, 'showAttempts'])->name('attempts.index');

    // Activity logs
    Route::get('activity-logs', [ActivityLogController::class, "index"])->name('activity-logs.index');
    Route::get('activity-logs/modal', [ActivityLogController::class, "modal"])->name('activity-logs.modal');
    Route::get('activity-logs/row/{id}', [ActivityLogController::class, "row"])->name('activity-logs.row');
});

// Landlord Routes
Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    Route::resource('permissions', PermissionController::class)->names('permissions');
    Route::resource('roles', RoleController::class)->names('roles');

    // Login attempts per user
    Route::get('attempts/{id}', [AuthController::class, 'showAttempts'])->name('attempts.index');

    // Profile Page [Show and Update]
    Route::get('profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Activity Logs by [id]
    Route::get('activity-logs/{id?}', [ActivityLogController::class, "index"])->name('activity-logs.index');
    Route::get('activity-logs/modal/{id?}', [ActivityLogController::class, "modal"])->name('activity-logs.modal');

    // Dashboard API Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('stats', [DashboardController::class, 'getStats'])->name('stats');
        Route::get('user-chart', [DashboardController::class, 'getUserChartData'])->name('user-chart');
        Route::get('tenant-chart', [DashboardController::class, 'getTenantChartData'])->name('tenant-chart');
        Route::get('email-chart', [DashboardController::class, 'getEmailChartData'])->name('email-chart');
        Route::get('module-stats', [DashboardController::class, 'getModuleStats'])->name('module-stats');
    });
});
