<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Landlord\SuperAdminApiController;
use Modules\Auth\Http\Controllers\Landlord\UserManagementApiController;
use Modules\Auth\Http\Controllers\Landlord\RolePermissionApiController;
use Modules\Auth\Http\Controllers\Landlord\LoginAttemptApiController;
use Modules\Auth\Http\Controllers\Landlord\ActivityLogApiController;
use Modules\Auth\Http\Controllers\Landlord\SettingsApiController;

/*
|--------------------------------------------------------------------------
| NextJS Admin API Routes
|--------------------------------------------------------------------------
|
| API routes specifically designed for NextJS frontend consumption
| These routes provide JSON responses for super admin functionality
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    
    // Super Admin Dashboard Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [SuperAdminApiController::class, 'dashboard'])->name('show');
        Route::get('/stats', [SuperAdminApiController::class, 'getStats'])->name('stats');
        Route::get('/recent-activities', [SuperAdminApiController::class, 'getRecentActivities'])->name('recent');
    });

    // User Management Routes  
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementApiController::class, 'index'])->name('admin.index');
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
});
