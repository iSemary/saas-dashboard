<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthApiController;
use Modules\Auth\Http\Controllers\Api\UserMetaApiController;
use Modules\Auth\Http\Controllers\Api\TenantSettingsApiController;
use Modules\Auth\Http\Controllers\Api\TenantProfileApiController;
use Modules\Auth\Http\Controllers\Api\TenantTwoFactorApiController;
use Modules\Auth\Http\Controllers\Api\TenantRoleApiController;
use Modules\Auth\Http\Controllers\Api\TenantPermissionApiController;
use Modules\Auth\Http\Controllers\Api\TenantUserApiController;
use Modules\Auth\Http\Controllers\Api\TenantActivityLogApiController;
use Modules\Auth\Http\Controllers\Api\TenantLoginAttemptApiController;
use Modules\Auth\Http\Controllers\Landlord\DashboardController as LandlordDashboardController;
use Modules\Auth\Http\Controllers\Landlord\SuperAdminApiController;
use Modules\Auth\Http\Controllers\Landlord\UserManagementApiController;
use Modules\Auth\Http\Controllers\Landlord\RolePermissionApiController;
use Modules\Auth\Http\Controllers\Landlord\LoginAttemptApiController;
use Modules\Auth\Http\Controllers\Landlord\ActivityLogApiController;
use Modules\Auth\Http\Controllers\Landlord\SettingsApiController;
use Modules\Auth\Http\Controllers\Api\PermissionGroupApiController;

// ─── Landlord Auth Routes (public — no auth:api middleware) ──────────
Route::prefix('landlord/auth')->name('landlord.auth.')->middleware(['throttle:60,1'])->group(function () {
    Route::post('login', [AuthApiController::class, 'login'])->name('login');
    Route::post('2fa/verify', [AuthApiController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [AuthApiController::class, 'resetPassword'])->name('reset-password');
});

// ─── Landlord Auth Routes (protected — require auth:api) ────────────
Route::prefix('landlord/auth')->name('landlord.auth.')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('me', [AuthApiController::class, 'me'])->name('me');
    Route::post('logout', [AuthApiController::class, 'logout'])->name('logout');
    Route::post('2fa/setup', [AuthApiController::class, 'setup2FA'])->name('2fa.setup');
    Route::post('2fa/confirm', [AuthApiController::class, 'confirm2FA'])->name('2fa.confirm');
    Route::post('2fa/disable', [AuthApiController::class, 'disable2FA'])->name('2fa.disable');
    Route::get('2fa/recovery-codes', [AuthApiController::class, 'getRecoveryCodes'])->name('2fa.recovery-codes');
});

// ─── Landlord Protected Routes ──────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {

    // Super Admin Dashboard Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [SuperAdminApiController::class, 'dashboard'])->name('show');
        Route::get('/stats', [SuperAdminApiController::class, 'getStats'])->name('stats');
        Route::get('/recent-activities', [SuperAdminApiController::class, 'getRecentActivities'])->name('recent');
        Route::get('/user-chart', [LandlordDashboardController::class, 'getUserChartData'])->name('user-chart');
        Route::get('/tenant-chart', [LandlordDashboardController::class, 'getTenantChartData'])->name('tenant-chart');
        Route::get('/email-chart', [LandlordDashboardController::class, 'getEmailChartData'])->name('email-chart');
        Route::get('/module-stats', [LandlordDashboardController::class, 'getModuleStats'])->name('module-stats');
    });

    // Activity logs (SPA alias — same handler as security/activity-logs)
    Route::get('activity-logs', [ActivityLogApiController::class, 'index'])->name('activity-logs.index');

    // Permissions list (SPA alias — same handler as roles/permissions)
    Route::get('permissions', [RolePermissionApiController::class, 'permissions'])->name('permissions.index');

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementApiController::class, 'index'])->name('landlord.index');
        Route::post('/', [UserManagementApiController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementApiController::class, 'show'])->name('show');
        Route::put('/{user}', [UserManagementApiController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementApiController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/roles', [UserManagementApiController::class, 'assignRoles'])->name('assign-roles');
        Route::delete('/{user}/roles', [UserManagementApiController::class, 'removeRoles'])->name('remove-roles');
        Route::get('/{user}/permissions', [UserManagementApiController::class, 'userPermissions'])->name('permissions');
    });

    // Role & Permission Management Routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolePermissionApiController::class, 'roles'])->name('index');
        Route::post('/', [RolePermissionApiController::class, 'createRole'])->name('store');
        Route::get('/{role}', [RolePermissionApiController::class, 'showRole'])->name('show');
        Route::put('/{role}', [RolePermissionApiController::class, 'updateRole'])->name('update');
        Route::delete('/{role}', [RolePermissionApiController::class, 'deleteRole'])->name('destroy');
        Route::post('/{role}/permissions', [RolePermissionApiController::class, 'assignPermissions'])->name('assign-permissions');

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [RolePermissionApiController::class, 'permissions'])->name('index');
            Route::post('/', [RolePermissionApiController::class, 'createPermission'])->name('store');
            Route::put('/{permission}', [RolePermissionApiController::class, 'updatePermission'])->name('update');
            Route::delete('/{permission}', [RolePermissionApiController::class, 'deletePermission'])->name('destroy');
        });
    });

    // Login Attempts Monitoring Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::prefix('login-attempts')->name('login-attempts.')->group(function () {
            Route::get('/', [LoginAttemptApiController::class, 'index'])->name('index');
            Route::get('/stats', [LoginAttemptApiController::class, 'getStats'])->name('stats');
            Route::get('/failed-attempts', [LoginAttemptApiController::class, 'getFailedAttempts'])->name('failed');
            Route::get('/recent-activity', [LoginAttemptApiController::class, 'getRecentActivity'])->name('recent');
            Route::post('/block-ip/{ip}', [LoginAttemptApiController::class, 'blockIP'])->name('block-ip');
            Route::post('/unblock-ip/{ip}', [LoginAttemptApiController::class, 'unblockIP'])->name('unblock-ip');
        });

        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogApiController::class, 'index'])->name('index');
            Route::get('/stats', [ActivityLogApiController::class, 'getStats'])->name('stats');
            Route::get('/user/{user}', [ActivityLogApiController::class, 'getUserActivity'])->name('user');
            Route::get('/system', [ActivityLogApiController::class, 'getSystemActivity'])->name('system');
            Route::post('/clear-old-logs', [ActivityLogApiController::class, 'clearOldLogs'])->name('clear');
        });
    });

    // System Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsApiController::class, 'index'])->name('index');
        Route::put('/', [SettingsApiController::class, 'update'])->name('update');
        Route::get('/security', [SettingsApiController::class, 'securitySettings'])->name('security');
        Route::put('/security', [SettingsApiController::class, 'updateSecurity'])->name('update-security');
        Route::get('/system', [SettingsApiController::class, 'systemSettings'])->name('system');
        Route::put('/system', [SettingsApiController::class, 'updateSystem'])->name('update-system');
    });

    // Permission Groups
    Route::apiResource('permission-groups', PermissionGroupApiController::class);
});

// ─── Tenant Auth Routes (public — no auth:api middleware) ──────────
Route::prefix('tenant/auth')->name('tenant.auth.')->middleware(['throttle:60,1'])->group(function () {
    Route::post('login', [AuthApiController::class, 'login'])->name('login');
    Route::post('2fa/verify', [AuthApiController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [AuthApiController::class, 'resetPassword'])->name('reset-password');
});

// ─── Tenant Auth Routes (protected — require auth:api) ────────────
Route::prefix('tenant/auth')->name('tenant.auth.')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('me', [AuthApiController::class, 'me'])->name('me');
    Route::post('logout', [AuthApiController::class, 'logout'])->name('logout');
    Route::post('2fa/setup', [AuthApiController::class, 'setup2FA'])->name('2fa.setup');
    Route::post('2fa/confirm', [AuthApiController::class, 'confirm2FA'])->name('2fa.confirm');
    Route::post('2fa/disable', [AuthApiController::class, 'disable2FA'])->name('2fa.disable');
    Route::get('2fa/recovery-codes', [AuthApiController::class, 'getRecoveryCodes'])->name('2fa.recovery-codes');
});

// ─── Public API routes (shared settings) ─────────────────────────
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('settings', [TenantSettingsApiController::class, 'index']);
    Route::put('settings', [TenantSettingsApiController::class, 'update']);
});

// ─── Tenant Protected Routes ──────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant-api', 'throttle:60,1'])->group(function () {
    // Roles
    Route::apiResource('roles', TenantRoleApiController::class);

    // Permissions
    Route::apiResource('permissions', TenantPermissionApiController::class)->except(['show']);

    // Users
    Route::apiResource('users', TenantUserApiController::class);
    Route::post('users/{id}/roles', [TenantUserApiController::class, 'assignRoles'])->name('users.assign-roles');

    // Activity Logs
    Route::get('activity-logs', [TenantActivityLogApiController::class, 'index'])->name('activity-logs.index');

    // Login Attempts
    Route::get('login-attempts', [TenantLoginAttemptApiController::class, 'index'])->name('login-attempts.index');

    // Settings
    Route::get('settings', [TenantSettingsApiController::class, 'index'])->name('settings.index');
    Route::put('settings', [TenantSettingsApiController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('profile', [TenantProfileApiController::class, 'show'])->name('profile.show');
    Route::put('profile', [TenantProfileApiController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [TenantProfileApiController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('profile/password', [TenantProfileApiController::class, 'changePassword'])->name('profile.password');

    // Two-Factor Auth
    Route::post('2fa/setup', [TenantTwoFactorApiController::class, 'setup'])->name('2fa.setup');
    Route::post('2fa/confirm', [TenantTwoFactorApiController::class, 'confirm'])->name('2fa.confirm');
    Route::post('2fa/disable', [TenantTwoFactorApiController::class, 'disable'])->name('2fa.disable');
});

// User Meta API routes (for user-specific settings like animations)
Route::prefix('user-meta')->middleware('auth:api')->group(function () {
    Route::get('/', [UserMetaApiController::class, 'index'])->name('api.user-meta.index');
    Route::get('/{key}', [UserMetaApiController::class, 'show'])->name('api.user-meta.show');
    Route::post('/', [UserMetaApiController::class, 'store'])->name('api.user-meta.store');
    Route::delete('/{key}', [UserMetaApiController::class, 'destroy'])->name('api.user-meta.destroy');
});
