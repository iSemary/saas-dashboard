<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\LeadController;
use Modules\CRM\Http\Controllers\OpportunityController;
use Modules\CRM\Http\Controllers\ContactController;
use Modules\CRM\Http\Controllers\CompanyController;
use Modules\CRM\Http\Controllers\ActivityController;

/*
|--------------------------------------------------------------------------
| CRM Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the CRM module.
|
*/

Route::middleware(['auth:web', 'tenant'])->group(function () {
    // Lead routes
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::get('/create', [LeadController::class, 'create'])->name('create');
        Route::post('/', [LeadController::class, 'store'])->name('store');
        Route::get('/{id}', [LeadController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LeadController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeadController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeadController::class, 'destroy'])->name('destroy');
        
        // Additional lead actions
        Route::post('/{id}/convert', [LeadController::class, 'convert'])->name('convert');
        Route::patch('/{id}/status', [LeadController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{id}/assign', [LeadController::class, 'assign'])->name('assign');
        Route::get('/search/query', [LeadController::class, 'search'])->name('search');
        Route::get('/statistics/overview', [LeadController::class, 'statistics'])->name('statistics');
    });

    // Opportunity routes
    Route::prefix('opportunities')->name('opportunities.')->group(function () {
        Route::get('/', [OpportunityController::class, 'index'])->name('index');
        Route::get('/create', [OpportunityController::class, 'create'])->name('create');
        Route::post('/', [OpportunityController::class, 'store'])->name('store');
        Route::get('/{id}', [OpportunityController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OpportunityController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OpportunityController::class, 'update'])->name('update');
        Route::delete('/{id}', [OpportunityController::class, 'destroy'])->name('destroy');
    });

    // Contact routes
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Company routes
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{id}', [CompanyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('destroy');
    });

    // Activity routes
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::get('/create', [ActivityController::class, 'create'])->name('create');
        Route::post('/', [ActivityController::class, 'store'])->name('store');
        Route::get('/{id}', [ActivityController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ActivityController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ActivityController::class, 'update'])->name('update');
        Route::delete('/{id}', [ActivityController::class, 'destroy'])->name('destroy');
    });
});
