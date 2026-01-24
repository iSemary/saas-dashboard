<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Guest\AuthController;
use Modules\Auth\Http\Controllers\PermissionController;
use Modules\Auth\Http\Controllers\PermissionGroupController;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\Landlord\UserController;
use Modules\Auth\Http\Controllers\Guest\OrganizationController;
use Modules\Auth\Http\Controllers\Guest\PasswordController;
use Modules\Auth\Http\Controllers\Guest\TwoFactorAuthController;
use Modules\Auth\Http\Controllers\Guest\ActivityLogController;
use Modules\Auth\Http\Controllers\Landlord\DashboardController;
use Modules\Auth\Http\Controllers\Tenant\ProfileController;
use Modules\Auth\Http\Controllers\Tenant\SettingsController;
use Modules\Auth\Http\Controllers\Tenant\TenantRoleController;
use Modules\Auth\Http\Controllers\Tenant\TenantPermissionController;
use Modules\Auth\Http\Controllers\Tenant\TenantUserManagementController;
use Modules\Auth\Http\Controllers\Tenant\TenantLoginAttemptController;
use Modules\Auth\Http\Controllers\Tenant\TenantActivityLogController;
use Modules\Customer\Http\Controllers\Tenant\BrandController;
use Modules\Customer\Http\Controllers\Tenant\ModuleDashboardController;
use App\Http\Controllers\TenantController;

// Routes for guests (no auth middleware)
Route::middleware('guest')->group(function () {
    // Check Organization
    Route::post('organization/check', [OrganizationController::class, 'check'])->name('organization.check');

    // Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Forget Password
    Route::get('password/forget', [PasswordController::class, 'showForgetForm'])->name('password.forget.show');
    Route::post('password/forget', [PasswordController::class, 'submitForgetForm'])->name('password.forget.submit');

    // Reset Password
    Route::get('password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset.show');
    Route::post('password/reset', [PasswordController::class, 'submitResetForm'])->name('password.reset.submit');
});

// Routes for authenticated users (auth middleware)
Route::middleware('auth')->group(function () {
    Route::middleware('2fa')->group(function () {
        Route::get('/', [TenantController::class, "index"])->name('home');
        
        // Tenant Dashboard API Routes
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('stats', [\Modules\Auth\Http\Controllers\Tenant\DashboardController::class, 'getStats'])->name('stats');
        });

        // Tenant Profile Routes
        Route::prefix('tenant')->name('tenant.')->group(function () {
            Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
            Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
            
            // Tenant Settings Routes
            Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::get('settings/notifications', [SettingsController::class, 'getNotificationSettings'])->name('settings.notifications');
            Route::get('settings/appearance', [SettingsController::class, 'getAppearanceSettings'])->name('settings.appearance');
            Route::get('settings/privacy', [SettingsController::class, 'getPrivacySettings'])->name('settings.privacy');
            Route::post('settings/export', [SettingsController::class, 'export'])->name('settings.export');
            Route::post('settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

            // Tenant Roles Management Routes
            Route::resource('roles', TenantRoleController::class)->except(['show'])->names('roles');
            Route::get('roles/{id}', [TenantRoleController::class, 'show'])->name('roles.show');
            Route::post('roles/{id}/restore', [TenantRoleController::class, 'restore'])->name('roles.restore');
            Route::post('roles/{id}/assign-permissions', [TenantRoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
            Route::post('roles/{id}/sync-permissions', [TenantRoleController::class, 'syncPermissions'])->name('roles.sync-permissions');
            Route::get('roles/statistics/data', [TenantRoleController::class, 'statistics'])->name('roles.statistics');

            // Tenant Permissions Management Routes
            Route::resource('permissions', TenantPermissionController::class)->except(['show'])->names('permissions');
            Route::get('permissions/{id}', [TenantPermissionController::class, 'show'])->name('permissions.show');
            Route::get('permissions/bulk/create-form', [TenantPermissionController::class, 'bulkCreateForm'])->name('permissions.bulk-create');
            Route::post('permissions/bulk/create', [TenantPermissionController::class, 'bulkCreate'])->name('permissions.bulk-store');
            Route::get('permissions/grouped/list', [TenantPermissionController::class, 'grouped'])->name('permissions.grouped');
            Route::get('permissions/statistics/data', [TenantPermissionController::class, 'statistics'])->name('permissions.statistics');

            // Tenant User Management Routes
            Route::resource('users', TenantUserManagementController::class)->except(['show'])->names('users');
            Route::get('users/{id}', [TenantUserManagementController::class, 'show'])->name('users.show');
            Route::post('users/{id}/activate', [TenantUserManagementController::class, 'activate'])->name('users.activate');
            Route::post('users/{id}/deactivate', [TenantUserManagementController::class, 'deactivate'])->name('users.deactivate');
            Route::post('users/bulk/activate', [TenantUserManagementController::class, 'bulkActivate'])->name('users.bulk-activate');
            Route::post('users/bulk/deactivate', [TenantUserManagementController::class, 'bulkDeactivate'])->name('users.bulk-deactivate');
            Route::post('users/bulk/delete', [TenantUserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
            Route::post('users/{id}/reset-password', [TenantUserManagementController::class, 'resetPassword'])->name('users.reset-password');
            Route::post('users/{id}/send-password-reset', [TenantUserManagementController::class, 'sendPasswordResetEmail'])->name('users.send-password-reset');
            Route::post('users/{id}/assign-roles', [TenantUserManagementController::class, 'assignRoles'])->name('users.assign-roles');
            Route::post('users/{id}/sync-roles', [TenantUserManagementController::class, 'syncRoles'])->name('users.sync-roles');
            Route::get('users/statistics/data', [TenantUserManagementController::class, 'statistics'])->name('users.statistics');

            // Tenant Login Attempts Routes
            Route::get('login-attempts/{id?}', [TenantLoginAttemptController::class, 'index'])->name('login-attempts.index');

            // Tenant Activity Logs Routes
            Route::get('activity-logs/{id?}', [TenantActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('activity-logs/modal/{id?}', [TenantActivityLogController::class, 'modal'])->name('activity-logs.modal');
            Route::get('activity-logs/row/{id}', [TenantActivityLogController::class, 'row'])->name('activity-logs.row');

            // Brand Management Routes
            Route::resource('brands', BrandController::class)->names('brands');
            Route::get('brands/{id}/modules', [BrandController::class, 'getModules'])->name('brands.modules');
            Route::post('brands/{id}/assign-modules', [BrandController::class, 'assignModules'])->name('brands.assign-modules');
            Route::get('brands/dashboard/data', [BrandController::class, 'getBrandsForDashboard'])->name('brands.dashboard-data');

            // Module Dashboard Routes
            Route::prefix('dashboard')->name('dashboard.')->group(function () {
                Route::get('hr', [ModuleDashboardController::class, 'hr'])->name('hr');
                Route::get('crm', [ModuleDashboardController::class, 'crm'])->name('crm');
                Route::get('pos', [ModuleDashboardController::class, 'pos'])->name('pos');
                Route::get('accounting', [ModuleDashboardController::class, 'accounting'])->name('accounting');
                Route::get('sales', [ModuleDashboardController::class, 'sales'])->name('sales');
                Route::get('inventory', [ModuleDashboardController::class, 'inventory'])->name('inventory');
            });
        });
    });

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Lock
    Route::middleware('2fa')->group(function () {
        Route::get('lock', [PasswordController::class, 'showLock'])->name('lock.show');
        Route::post('lock', [PasswordController::class, 'lock'])->name('lock.submit');
        Route::post('unlock', [PasswordController::class, 'unlock'])->name('unlock.submit');
    });

    // 2FA
    Route::get('2fa/setup', [TwoFactorAuthController::class, 'showSetupForm'])->name('2fa.setup');
    Route::get('2fa/validate', [TwoFactorAuthController::class, 'showValidateForm'])->name('2fa.validate');
    Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('2fa/check', [TwoFactorAuthController::class, 'check'])->name('2fa.check');

    Route::middleware('2fa')->group(function () {
        Route::post('/2fa/reset', [TwoFactorAuthController::class, "reset"])->name('2fa.reset');
    });



    // Login Attempts
    Route::get('attempts', [TwoFactorAuthController::class, 'showAttempts'])->name('attempts.index');

    // Activity logs
    Route::get('activity-logs', [ActivityLogController::class, "index"])->name('activity-logs.index');
    Route::get('activity-logs/modal', [ActivityLogController::class, "modal"])->name('activity-logs.modal');
    Route::get('activity-logs/row/{id}', [ActivityLogController::class, "row"])->name('activity-logs.row');
});

// Landlord Routes
Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    Route::resource('permissions', PermissionController::class)->names('permissions');
    Route::resource('permission-groups', PermissionGroupController::class)->names('permission-groups');
    Route::resource('roles', RoleController::class)->names('roles');

    // Login attempts per user
    Route::get('attempts/{id}', [AuthController::class, 'showAttempts'])->name('attempts.index');

    // Profile Page [Show and Update]
    Route::get('profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Activity Logs by [id]
    Route::get('activity-logs/{id?}', [ActivityLogController::class, "index"])->name('activity-logs.index');
    Route::get('activity-logs/modal/{id?}', [ActivityLogController::class, "modal"])->name('activity-logs.modal');

    // Dashboard API Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('stats', [DashboardController::class, 'getStats'])->name('stats');
        Route::get('user-chart', [DashboardController::class, 'getUserChartData'])->name('user-chart');
        Route::get('tenant-chart', [DashboardController::class, 'getTenantChartData'])->name('tenant-chart');
        Route::get('email-chart', [DashboardController::class, 'getEmailChartData'])->name('email-chart');
        Route::get('module-stats', [DashboardController::class, 'getModuleStats'])->name('module-stats');
    });
});
