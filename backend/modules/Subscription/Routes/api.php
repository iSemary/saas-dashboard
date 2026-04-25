<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\PlanApiController;
use Modules\Subscription\Http\Controllers\Api\SubscriptionApiController;
use Modules\Subscription\Http\Controllers\Api\OnboardingApiController;

// ─── Landlord Subscriptions & Plans ─────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('plans', PlanApiController::class);
    Route::apiResource('subscriptions', SubscriptionApiController::class)->only(['index', 'destroy']);

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::post('select-plan', [OnboardingApiController::class, 'selectPlan'])->name('select-plan');
        Route::post('select-modules', [OnboardingApiController::class, 'selectModules'])->name('select-modules');
    });
});
