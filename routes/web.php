<?php

use App\Http\Controllers\TenantController;
use Modules\Auth\Http\Controllers\Guest\AuthController;
use Modules\Auth\Http\Controllers\Guest\OrganizationController;
use Modules\Auth\Http\Controllers\Guest\PasswordController;
use Modules\Auth\Http\Controllers\Guest\TwoFactorAuthController;
use Illuminate\Support\Facades\Route;


// Routes for authenticated users (auth middleware)
Route::middleware('auth')->group(function () {

    Route::middleware('2fa')->group(function () {
        Route::get('/', [TenantController::class, "index"])->name('home');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    // 2FA
    Route::get('2fa/setup', [TwoFactorAuthController::class, 'showSetupForm'])->name('2fa.setup');
    Route::get('2fa/validate', [TwoFactorAuthController::class, 'showValidateForm'])->name('2fa.validate');
    Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('2fa/check', [TwoFactorAuthController::class, 'check'])->name('2fa.check');
});

// Routes for guests (no auth middleware)
Route::middleware('guest')->group(function () {
    // Check Organization
    Route::post('organization/check', [OrganizationController::class, 'check'])->name('organization.check');

    // Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Registration
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register.show');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit');

    // Forget Password
    Route::get('password/forget', [PasswordController::class, 'showForgetForm'])->name('password.forget.show');
    Route::post('password/forget', [PasswordController::class, 'submitForgetForm'])->name('password.forget.submit');

    // Reset Password
    Route::get('password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset.show');
    Route::post('password/reset', [PasswordController::class, 'submitResetForm'])->name('password.reset.submit');
});
