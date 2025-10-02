<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Api\PaymentApiController;
use Modules\Payment\Http\Controllers\Api\WebhookApiController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1')->group(function () {
    
    // Public webhook endpoints (no auth required)
    Route::prefix('webhooks')->group(function () {
        Route::post('stripe', [WebhookApiController::class, 'stripe'])->name('webhooks.stripe');
        Route::post('paypal', [WebhookApiController::class, 'paypal'])->name('webhooks.paypal');
        Route::post('razorpay', [WebhookApiController::class, 'razorpay'])->name('webhooks.razorpay');
        Route::post('adyen', [WebhookApiController::class, 'adyen'])->name('webhooks.adyen');
        Route::post('square', [WebhookApiController::class, 'square'])->name('webhooks.square');
        Route::post('{gateway}', [WebhookApiController::class, 'generic'])->name('webhooks.generic');
    });
    
    // Authenticated payment endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Payment processing
        Route::prefix('payments')->group(function () {
            Route::post('process', [PaymentApiController::class, 'processPayment'])->name('api.payments.process');
            Route::post('authorize', [PaymentApiController::class, 'authorizePayment'])->name('api.payments.authorize');
            Route::post('{transactionId}/capture', [PaymentApiController::class, 'capturePayment'])->name('api.payments.capture');
            Route::post('{transactionId}/void', [PaymentApiController::class, 'voidPayment'])->name('api.payments.void');
            Route::get('{transactionId}', [PaymentApiController::class, 'getTransaction'])->name('api.payments.show');
        });
        
        // Refund processing
        Route::prefix('refunds')->group(function () {
            Route::post('process', [PaymentApiController::class, 'processRefund'])->name('api.refunds.process');
        });
        
        // Payment methods
        Route::prefix('payment-methods')->group(function () {
            Route::get('/', [PaymentApiController::class, 'getPaymentMethods'])->name('api.payment-methods.index');
            Route::post('{paymentMethodId}/test', [PaymentApiController::class, 'testPaymentMethod'])->name('api.payment-methods.test');
        });
        
    });
    
});
