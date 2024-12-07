<?php

use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\Guest\OrganizationController;
use App\Http\Controllers\Guest\PasswordController;
use App\Http\Controllers\Guest\TwoFactorAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Check Organization
Route::post('/organization/check', [OrganizationController::class, 'check'])->name('organization.check');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Registration
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Forget Password
Route::get('/password/forget', [PasswordController::class, 'showForgetForm'])->name('password.forget.show');
Route::post('/password/forget', [PasswordController::class, 'submitForgetForm'])->name('password.forget.submit');

// Reset Password
Route::get('/password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset.show');
Route::post('/password/reset', [PasswordController::class, 'submitResetForm'])->name('password.reset.submit');

// 2FA
Route::get('/2fa', [TwoFactorAuthController::class, 'showForm'])->name('2fa.show');
Route::post('/2fa', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
