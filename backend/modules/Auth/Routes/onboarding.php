<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\OnboardingController;

/*
|--------------------------------------------------------------------------
| Onboarding Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the onboarding process after user registration
| and email verification. These routes guide users through brand creation,
| plan selection, module selection, and free trial setup.
|
*/

Route::prefix('onboarding')->name('onboarding.')->middleware(['auth:web', 'verified'])->group(function () {
    
    // Welcome step
    Route::get('/', [OnboardingController::class, 'welcome'])->name('welcome');
    
    // Brand creation step
    Route::get('/create-brand', [OnboardingController::class, 'createBrand'])->name('create-brand');
    Route::post('/create-brand', [OnboardingController::class, 'storeBrand'])->name('store-brand');
    
    // Plan selection step
    Route::get('/select-plan', [OnboardingController::class, 'selectPlan'])->name('select-plan');
    Route::post('/select-plan', [OnboardingController::class, 'storePlan'])->name('store-plan');
    
    // Module selection step
    Route::get('/select-modules', [OnboardingController::class, 'selectModules'])->name('select-modules');
    Route::post('/select-modules', [OnboardingController::class, 'storeModules'])->name('store-modules');
    
    // Completion step
    Route::get('/complete', [OnboardingController::class, 'complete'])->name('complete');
    Route::post('/complete', [OnboardingController::class, 'redirectToDashboard'])->name('redirect-dashboard');
    
});
