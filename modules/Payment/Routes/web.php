<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentMethodController;
use Modules\Payment\Http\Controllers\PaymentTransactionController;
use Modules\Payment\Http\Controllers\RefundController;
use Modules\Payment\Http\Controllers\ChargebackController;
use Modules\Payment\Http\Controllers\PaymentRoutingController;
use Modules\Payment\Http\Controllers\PaymentAnalyticsController;
use Modules\Payment\Http\Controllers\PaymentSettingsController;
use Modules\Payment\Http\Controllers\PaymentDashboardController;
use Modules\Payment\Http\Controllers\PaymentLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    
    // Payment Dashboard
    Route::get('payment-dashboard', [PaymentDashboardController::class, 'index'])->name('payment-dashboard');
    
    // Payment Methods
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::post('payment-methods/{id}/restore', [PaymentMethodController::class, 'restore'])->name('payment-methods.restore');
    Route::post('payment-methods/{id}/test', [PaymentMethodController::class, 'test'])->name('payment-methods.test');
    Route::get('payment-methods/gateway-config/{processor}', [PaymentMethodController::class, 'getGatewayConfig'])->name('payment-methods.gateway-config');
    Route::get('payment-methods/export', [PaymentMethodController::class, 'export'])->name('payment-methods.export');
    
    // Payment Transactions
    Route::resource('payment-transactions', PaymentTransactionController::class)->only(['index', 'show']);
    Route::post('payment-transactions/{id}/capture', [PaymentTransactionController::class, 'capture'])->name('payment-transactions.capture');
    Route::post('payment-transactions/{id}/void', [PaymentTransactionController::class, 'void'])->name('payment-transactions.void');
    Route::get('payment-transactions/analytics', [PaymentTransactionController::class, 'analytics'])->name('payment-transactions.analytics');
    Route::get('payment-transactions/export', [PaymentTransactionController::class, 'export'])->name('payment-transactions.export');
    
    // Refunds
    Route::resource('refunds', RefundController::class);
    Route::post('refunds/{id}/process', [RefundController::class, 'process'])->name('refunds.process');
    Route::post('refunds/{id}/cancel', [RefundController::class, 'cancel'])->name('refunds.cancel');
    
    // Chargebacks
    Route::resource('chargebacks', ChargebackController::class);
    Route::post('chargebacks/{id}/accept', [ChargebackController::class, 'accept'])->name('chargebacks.accept');
    Route::post('chargebacks/{id}/dispute', [ChargebackController::class, 'dispute'])->name('chargebacks.dispute');
    Route::post('chargebacks/{id}/evidence', [ChargebackController::class, 'submitEvidence'])->name('chargebacks.evidence');
    
    // Payment Routing
    Route::resource('payment-routing', PaymentRoutingController::class)->names('payment-routing');
    Route::post('payment-routing/{id}/toggle', [PaymentRoutingController::class, 'toggle'])->name('payment-routing.toggle');
    Route::get('payment-routing/analytics', [PaymentRoutingController::class, 'analytics'])->name('payment-routing.analytics');
    Route::post('payment-routing/optimize', [PaymentRoutingController::class, 'optimize'])->name('payment-routing.optimize');
    
    // Payment Analytics
    Route::get('payment-analytics', [PaymentAnalyticsController::class, 'index'])->name('payment-analytics.index');
    Route::get('payment-analytics/revenue', [PaymentAnalyticsController::class, 'revenue'])->name('payment-analytics.revenue');
    Route::get('payment-analytics/performance', [PaymentAnalyticsController::class, 'performance'])->name('payment-analytics.performance');
    Route::get('payment-analytics/export', [PaymentAnalyticsController::class, 'export'])->name('payment-analytics.export');
    
    // Payment Settings
    Route::get('payment-settings', [PaymentSettingsController::class, 'index'])->name('payment-settings.index');
    Route::post('payment-settings', [PaymentSettingsController::class, 'update'])->name('payment-settings.update');
    Route::post('payment-settings/test-webhook', [PaymentSettingsController::class, 'testWebhook'])->name('payment-settings.test-webhook');
    
    // Payment Logs
    Route::resource('payment-logs', PaymentLogController::class)->only(['index', 'show']);
});
