<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Presentation\Http\Controllers\Api\StockMoveApiController;
use Modules\Inventory\Presentation\Http\Controllers\Api\WarehouseApiController;

// ─── Tenant Inventory API Routes ─────────────────────────────────────────────
Route::prefix('tenant/inventory')
    ->name('tenant.inventory.')
    ->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])
    ->group(function () {

        // Warehouses
        Route::get('warehouses/{id}/stock-summary', [WarehouseApiController::class, 'stockSummary'])->name('warehouses.stock-summary');
        Route::apiResource('warehouses', WarehouseApiController::class);

        // Stock Moves
        Route::patch('stock-moves/{id}/confirm', [StockMoveApiController::class, 'confirm'])->name('stock-moves.confirm');
        Route::patch('stock-moves/{id}/complete', [StockMoveApiController::class, 'complete'])->name('stock-moves.complete');
        Route::patch('stock-moves/{id}/cancel', [StockMoveApiController::class, 'cancel'])->name('stock-moves.cancel');
        Route::apiResource('stock-moves', StockMoveApiController::class)->except(['update']);
    });
