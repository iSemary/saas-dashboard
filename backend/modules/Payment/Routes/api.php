<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Api\PaymentApiController;
use Modules\Payment\Http\Controllers\Api\PaymentMethodApiController;
use Modules\Payment\Http\Controllers\Api\PaymentAnalyticsApiController;
use Modules\Payment\Http\Controllers\Api\WebhookApiController;

// ─── Landlord Payment Methods & Analytics ────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('payment-methods', PaymentMethodApiController::class)->except(['show', 'update']);
    Route::get('payment-analytics', [PaymentAnalyticsApiController::class, 'index'])->name('payment-analytics.index');
});

// ─── Public Webhook Endpoints (no auth required) ────────────────
Route::prefix('v1/webhooks')->group(function () {
    Route::post('stripe', [WebhookApiController::class, 'stripe'])->name('webhooks.stripe');
    Route::post('paypal', [WebhookApiController::class, 'paypal'])->name('webhooks.paypal');
    Route::post('razorpay', [WebhookApiController::class, 'razorpay'])->name('webhooks.razorpay');
    Route::post('adyen', [WebhookApiController::class, 'adyen'])->name('webhooks.adyen');
    Route::post('square', [WebhookApiController::class, 'square'])->name('webhooks.square');
    Route::post('{gateway}', [WebhookApiController::class, 'generic'])->name('webhooks.generic');
});

// ─── Authenticated Payment Processing ────────────────────────────
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('payments')->group(function () {
        Route::post('process', [PaymentApiController::class, 'processPayment'])->name('api.payments.process');
        Route::post('authorize', [PaymentApiController::class, 'authorizePayment'])->name('api.payments.authorize');
        Route::post('{transactionId}/capture', [PaymentApiController::class, 'capturePayment'])->name('api.payments.capture');
        Route::post('{transactionId}/void', [PaymentApiController::class, 'voidPayment'])->name('api.payments.void');
        Route::get('{transactionId}', [PaymentApiController::class, 'getTransaction'])->name('api.payments.show');
    });

    Route::prefix('refunds')->group(function () {
        Route::post('process', [PaymentApiController::class, 'processRefund'])->name('api.refunds.process');
    });

    Route::prefix('payment-methods')->group(function () {
        Route::get('/', [PaymentApiController::class, 'getPaymentMethods'])->name('api.payment-methods.index');
        Route::post('{paymentMethodId}/test', [PaymentApiController::class, 'testPaymentMethod'])->name('api.payment-methods.test');
    });
});
