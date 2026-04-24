<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Api\CompanyApiController;
use Modules\CRM\Http\Controllers\Api\ContactApiController;

/*
 *--------------------------------------------------------------------------
 * CRM API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for the CRM module.
 *
 */

Route::middleware('auth:api')->prefix('crm')->group(function () {
    // Company routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyApiController::class, 'index'])->name('api.crm.companies.index');
        Route::post('/', [CompanyApiController::class, 'store'])->name('api.crm.companies.store');
        Route::get('/{id}', [CompanyApiController::class, 'show'])->name('api.crm.companies.show');
        Route::put('/{id}', [CompanyApiController::class, 'update'])->name('api.crm.companies.update');
        Route::delete('/{id}', [CompanyApiController::class, 'destroy'])->name('api.crm.companies.destroy');
        Route::get('/{id}/activity', [CompanyApiController::class, 'activity'])->name('api.crm.companies.activity');
        Route::post('/bulk-delete', [CompanyApiController::class, 'bulkDelete'])->name('api.crm.companies.bulk-delete');
    });

    // Contact routes
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactApiController::class, 'index'])->name('api.crm.contacts.index');
        Route::post('/', [ContactApiController::class, 'store'])->name('api.crm.contacts.store');
        Route::get('/{id}', [ContactApiController::class, 'show'])->name('api.crm.contacts.show');
        Route::put('/{id}', [ContactApiController::class, 'update'])->name('api.crm.contacts.update');
        Route::delete('/{id}', [ContactApiController::class, 'destroy'])->name('api.crm.contacts.destroy');
        Route::get('/{id}/activity', [ContactApiController::class, 'activity'])->name('api.crm.contacts.activity');
        Route::post('/bulk-delete', [ContactApiController::class, 'bulkDelete'])->name('api.crm.contacts.bulk-delete');
    });
});
