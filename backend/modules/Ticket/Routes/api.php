<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\Api\TicketApiController;
use Modules\Ticket\Http\Controllers\Api\TenantTicketApiController;

// ─── Landlord Tickets ────────────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('tickets', TicketApiController::class);
});

// ─── Tenant Tickets ──────────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::apiResource('tickets', TenantTicketApiController::class);
    Route::get('tickets/kanban-data', [TenantTicketApiController::class, 'kanbanData'])->name('tickets.kanban');
    Route::get('tickets/stats', [TenantTicketApiController::class, 'stats'])->name('tickets.stats');
    Route::patch('tickets/{id}/status', [TenantTicketApiController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('tickets/{id}/assign', [TenantTicketApiController::class, 'assign'])->name('tickets.assign');
});
