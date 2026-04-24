<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\Api\EmailTemplateApiController;
use Modules\Email\Http\Controllers\Api\EmailCredentialApiController;

/*
 *--------------------------------------------------------------------------
 * Email API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for the Email module.
 *
 */

Route::middleware('auth:api')->prefix('email')->group(function () {
    // Email Templates
    Route::prefix('templates')->group(function () {
        Route::get('/', [EmailTemplateApiController::class, 'index'])->name('api.email.templates.index');
        Route::post('/', [EmailTemplateApiController::class, 'store'])->name('api.email.templates.store');
        Route::get('/{id}', [EmailTemplateApiController::class, 'show'])->name('api.email.templates.show');
        Route::put('/{id}', [EmailTemplateApiController::class, 'update'])->name('api.email.templates.update');
        Route::delete('/{id}', [EmailTemplateApiController::class, 'destroy'])->name('api.email.templates.destroy');
        Route::post('/{id}/test', [EmailTemplateApiController::class, 'sendTest'])->name('api.email.templates.test');
    });

    // SMTP Configuration
    Route::prefix('smtp')->group(function () {
        Route::get('/', [EmailCredentialApiController::class, 'index'])->name('api.email.smtp.index');
        Route::post('/', [EmailCredentialApiController::class, 'store'])->name('api.email.smtp.store');
        Route::get('/{id}', [EmailCredentialApiController::class, 'show'])->name('api.email.smtp.show');
        Route::put('/{id}', [EmailCredentialApiController::class, 'update'])->name('api.email.smtp.update');
        Route::delete('/{id}', [EmailCredentialApiController::class, 'destroy'])->name('api.email.smtp.destroy');
        Route::post('/{id}/test', [EmailCredentialApiController::class, 'testConnection'])->name('api.email.smtp.test');
    });
});
