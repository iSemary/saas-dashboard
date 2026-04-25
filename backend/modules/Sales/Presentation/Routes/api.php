<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Presentation\Http\Controllers\Api\SalesClientApiController;
use Modules\Sales\Presentation\Http\Controllers\Api\SalesOrderApiController;

// ─── Tenant Sales API Routes ─────────────────────────────────────────────────
Route::prefix('tenant/sales')
    ->name('tenant.sales.')
    ->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])
    ->group(function () {

        // Orders
        Route::get('summary', [SalesOrderApiController::class, 'summary'])->name('orders.summary');
        Route::post('orders/bulk-delete', [SalesOrderApiController::class, 'bulkDelete'])->name('orders.bulk-delete');
        Route::patch('orders/{id}/cancel', [SalesOrderApiController::class, 'cancel'])->name('orders.cancel');
        Route::apiResource('orders', SalesOrderApiController::class)->except(['update']);

        // Clients
        Route::apiResource('clients', SalesClientApiController::class);
    });
