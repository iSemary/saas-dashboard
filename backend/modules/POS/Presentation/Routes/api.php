<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\Presentation\Http\Controllers\Api\BarcodeApiController;
use Modules\POS\Presentation\Http\Controllers\Api\CategoryApiController;
use Modules\POS\Presentation\Http\Controllers\Api\DamagedApiController;
use Modules\POS\Presentation\Http\Controllers\Api\OfferPriceApiController;
use Modules\POS\Presentation\Http\Controllers\Api\PosDashboardApiController;
use Modules\POS\Presentation\Http\Controllers\Api\ProductApiController;
use Modules\POS\Presentation\Http\Controllers\Api\SubCategoryApiController;
use Modules\POS\Presentation\Http\Controllers\Api\TagApiController;

// ─── Tenant POS API Routes ───────────────────────────────────────────────────
Route::prefix('tenant/pos')
    ->name('tenant.pos.')
    ->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [PosDashboardApiController::class, 'index'])->name('dashboard');

        // Products
        Route::post('products/bulk-delete', [ProductApiController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::get('products/barcode/{barcode}', [ProductApiController::class, 'searchByBarcode'])->name('products.barcode-search');
        Route::patch('products/{id}/stock', [ProductApiController::class, 'changeStock'])->name('products.change-stock');
        Route::apiResource('products', ProductApiController::class);

        // Categories
        Route::post('categories/bulk-delete', [CategoryApiController::class, 'bulkDelete'])->name('categories.bulk-delete');
        Route::apiResource('categories', CategoryApiController::class);

        // Sub-categories
        Route::post('sub-categories/bulk-delete', [SubCategoryApiController::class, 'bulkDelete'])->name('sub-categories.bulk-delete');
        Route::apiResource('sub-categories', SubCategoryApiController::class);

        // Barcodes
        Route::get('barcodes/search/{barcode}', [BarcodeApiController::class, 'search'])->name('barcodes.search');
        Route::get('barcodes', [BarcodeApiController::class, 'index'])->name('barcodes.index');
        Route::post('barcodes', [BarcodeApiController::class, 'store'])->name('barcodes.store');
        Route::delete('barcodes/{id}', [BarcodeApiController::class, 'destroy'])->name('barcodes.destroy');

        // Tags
        Route::get('tags', [TagApiController::class, 'index'])->name('tags.index');
        Route::post('tags', [TagApiController::class, 'store'])->name('tags.store');
        Route::delete('tags/{id}', [TagApiController::class, 'destroy'])->name('tags.destroy');

        // Offer Prices
        Route::apiResource('offer-prices', OfferPriceApiController::class);

        // Damaged
        Route::get('damaged', [DamagedApiController::class, 'index'])->name('damaged.index');
        Route::post('damaged', [DamagedApiController::class, 'store'])->name('damaged.store');
        Route::get('damaged/{id}', [DamagedApiController::class, 'show'])->name('damaged.show');
        Route::delete('damaged/{id}', [DamagedApiController::class, 'destroy'])->name('damaged.destroy');
    });
