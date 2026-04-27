<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\PlanApiController;
use Modules\Subscription\Http\Controllers\Api\SubscriptionApiController;
use Modules\Subscription\Http\Controllers\Api\OnboardingApiController;
use Modules\Subscription\Http\Controllers\Api\TenantBillingApiController;

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

// ─── Tenant Billing & Subscription ─────────────────────────────────
Route::prefix('tenant/billing')->name('tenant.billing.')->middleware(['auth:api', 'tenant-api', 'permission:read.billing', 'throttle:60,1'])->group(function () {
    // Overview
    Route::get('/overview', [TenantBillingApiController::class, 'overview'])->name('overview');
    Route::get('/upcoming-invoice', [TenantBillingApiController::class, 'upcomingInvoice'])->name('upcoming-invoice');

    // Plans
    Route::get('/plans', [TenantBillingApiController::class, 'plans'])->name('plans.index');
    Route::post('/plans/subscribe', [TenantBillingApiController::class, 'subscribeToPlan'])->name('plans.subscribe')->middleware('permission:subscribe.plans');
    Route::post('/plans/change', [TenantBillingApiController::class, 'changePlan'])->name('plans.change')->middleware('permission:subscribe.plans');
    Route::post('/plans/cancel', [TenantBillingApiController::class, 'cancelPlan'])->name('plans.cancel')->middleware('permission:cancel.plans');

    // Modules (Add-ons)
    Route::get('/modules', [TenantBillingApiController::class, 'modules'])->name('modules.index');
    Route::post('/modules/subscribe', [TenantBillingApiController::class, 'subscribeToModule'])->name('modules.subscribe')->middleware('permission:subscribe.modules');
    Route::post('/modules/unsubscribe', [TenantBillingApiController::class, 'unsubscribeFromModule'])->name('modules.unsubscribe')->middleware('permission:unsubscribe.modules');
    Route::get('/modules/proration-preview', [TenantBillingApiController::class, 'previewModuleProration'])->name('modules.proration-preview');

    // Invoices
    Route::get('/invoices', [TenantBillingApiController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{id}', [TenantBillingApiController::class, 'invoiceDetails'])->name('invoices.show');
    Route::get('/invoices/{id}/download', [TenantBillingApiController::class, 'downloadInvoice'])->name('invoices.download');
    Route::post('/invoices/{id}/pay', [TenantBillingApiController::class, 'payInvoice'])->name('invoices.pay')->middleware('permission:pay.invoices');

    // Payments
    Route::get('/payments', [TenantBillingApiController::class, 'payments'])->name('payments.index');
    Route::post('/payments/{id}/retry', [TenantBillingApiController::class, 'retryPayment'])->name('payments.retry')->middleware('permission:retry.payments');

    // Payment Methods
    Route::get('/payment-methods', [TenantBillingApiController::class, 'paymentMethods'])->name('payment-methods.index');
    Route::post('/payment-methods/setup-intent', [TenantBillingApiController::class, 'createSetupIntent'])->name('payment-methods.setup-intent');
    Route::post('/payment-methods/attach', [TenantBillingApiController::class, 'attachPaymentMethod'])->name('payment-methods.attach')->middleware('permission:create.payment_methods');
    Route::post('/payment-methods/{id}/default', [TenantBillingApiController::class, 'setDefaultPaymentMethod'])->name('payment-methods.set-default')->middleware('permission:update.payment_methods');
    Route::delete('/payment-methods/{id}', [TenantBillingApiController::class, 'removePaymentMethod'])->name('payment-methods.destroy')->middleware('permission:delete.payment_methods');
});
