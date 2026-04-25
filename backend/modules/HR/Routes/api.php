<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Api\HrApiController;

// ─── Tenant HR Module ────────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('modules/hr', [HrApiController::class, 'index'])->name('modules.hr');
});
