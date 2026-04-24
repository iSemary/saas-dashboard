<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NextJS Tenant API Routes
|--------------------------------------------------------------------------
|
| API routes for tenant dashboard frontend consumption.
| These require auth:api + tenant_roles middleware.
|
*/

Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {

    // ─── Dashboard ────────────────────────────────────────────────
    Route::get('dashboard/stats', [\Modules\Tenant\Http\Controllers\Api\TenantDashboardApiController::class, 'stats'])->name('dashboard.stats');

    // ─── Roles ────────────────────────────────────────────────────
    Route::apiResource('roles', \Modules\Auth\Http\Controllers\Api\TenantRoleApiController::class);

    // ─── Permissions ──────────────────────────────────────────────
    Route::apiResource('permissions', \Modules\Auth\Http\Controllers\Api\TenantPermissionApiController::class)->except(['show']);

    // ─── Users ────────────────────────────────────────────────────
    Route::apiResource('users', \Modules\Auth\Http\Controllers\Api\TenantUserApiController::class);
    Route::post('users/{id}/roles', [\Modules\Auth\Http\Controllers\Api\TenantUserApiController::class, 'assignRoles'])->name('users.assign-roles');

    // ─── Activity Logs ────────────────────────────────────────────
    Route::get('activity-logs', [\Modules\Auth\Http\Controllers\Api\TenantActivityLogApiController::class, 'index'])->name('activity-logs.index');

    // ─── Login Attempts ───────────────────────────────────────────
    Route::get('login-attempts', [\Modules\Auth\Http\Controllers\Api\TenantLoginAttemptApiController::class, 'index'])->name('login-attempts.index');

    // ─── Settings ─────────────────────────────────────────────────
    Route::get('settings', [\Modules\Auth\Http\Controllers\Api\TenantSettingsApiController::class, 'index'])->name('settings.index');
    Route::put('settings', [\Modules\Auth\Http\Controllers\Api\TenantSettingsApiController::class, 'update'])->name('settings.update');

    // ─── Profile ──────────────────────────────────────────────────
    Route::get('profile', [\Modules\Auth\Http\Controllers\Api\TenantProfileApiController::class, 'show'])->name('profile.show');
    Route::put('profile', [\Modules\Auth\Http\Controllers\Api\TenantProfileApiController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [\Modules\Auth\Http\Controllers\Api\TenantProfileApiController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('profile/password', [\Modules\Auth\Http\Controllers\Api\TenantProfileApiController::class, 'changePassword'])->name('profile.password');

    // ─── Two-Factor Auth ──────────────────────────────────────────
    Route::post('2fa/setup', [\Modules\Auth\Http\Controllers\Api\TenantTwoFactorApiController::class, 'setup'])->name('2fa.setup');
    Route::post('2fa/confirm', [\Modules\Auth\Http\Controllers\Api\TenantTwoFactorApiController::class, 'confirm'])->name('2fa.confirm');
    Route::post('2fa/disable', [\Modules\Auth\Http\Controllers\Api\TenantTwoFactorApiController::class, 'disable'])->name('2fa.disable');

    // ─── Brands ───────────────────────────────────────────────────
    Route::apiResource('brands', \Modules\Customer\Http\Controllers\Api\Tenant\BrandApiController::class);

    // ─── Branches ─────────────────────────────────────────────────
    Route::apiResource('branches', \Modules\Customer\Http\Controllers\Api\Tenant\BranchApiController::class);

    // ─── Tickets ──────────────────────────────────────────────────
    Route::apiResource('tickets', \Modules\Ticket\Http\Controllers\Api\TenantTicketApiController::class);
    Route::get('tickets/kanban-data', [\Modules\Ticket\Http\Controllers\Api\TenantTicketApiController::class, 'kanbanData'])->name('tickets.kanban');
    Route::get('tickets/stats', [\Modules\Ticket\Http\Controllers\Api\TenantTicketApiController::class, 'stats'])->name('tickets.stats');
    Route::patch('tickets/{id}/status', [\Modules\Ticket\Http\Controllers\Api\TenantTicketApiController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('tickets/{id}/assign', [\Modules\Ticket\Http\Controllers\Api\TenantTicketApiController::class, 'assign'])->name('tickets.assign');

    // ─── Modules ──────────────────────────────────────────────────
    Route::get('modules/crm', [\Modules\CRM\Http\Controllers\Api\CrmApiController::class, 'index'])->name('modules.crm');
    Route::get('modules/hr', [\Modules\HR\Http\Controllers\Api\HrApiController::class, 'index'])->name('modules.hr');
    Route::get('modules/pos', [\Modules\Sales\Http\Controllers\Api\PosApiController::class, 'index'])->name('modules.pos');
});
