<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\Api\PosApiController;

// ─── Tenant POS Module ───────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('modules/pos', [PosApiController::class, 'index'])->name('modules.pos');
});

// ─── Sales DDD Routes ────────────────────────────────────────────
require __DIR__ . '/../Presentation/Routes/api.php';
