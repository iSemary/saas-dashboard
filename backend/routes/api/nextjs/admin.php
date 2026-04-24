<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthApiController;
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

// ─── Admin Auth Routes (public — no auth:api middleware) ──────────
Route::prefix('admin/auth')->name('admin.auth.')->middleware(['throttle:60,1'])->group(function () {
    Route::post('login', [AuthApiController::class, 'login'])->name('login');
    Route::post('2fa/verify', [AuthApiController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [AuthApiController::class, 'resetPassword'])->name('reset-password');
});

// ─── Admin Auth Routes (protected — require auth:api) ────────────
Route::prefix('admin/auth')->name('admin.auth.')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('me', [AuthApiController::class, 'me'])->name('me');
    Route::post('logout', [AuthApiController::class, 'logout'])->name('logout');
    Route::post('2fa/setup', [AuthApiController::class, 'setup2FA'])->name('2fa.setup');
    Route::post('2fa/confirm', [AuthApiController::class, 'confirm2FA'])->name('2fa.confirm');
    Route::post('2fa/disable', [AuthApiController::class, 'disable2FA'])->name('2fa.disable');
    Route::get('2fa/recovery-codes', [AuthApiController::class, 'getRecoveryCodes'])->name('2fa.recovery-codes');
});

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

    // ─── Tenant Management ─────────────────────────────────────────
    Route::apiResource('tenants', \Modules\Tenant\Http\Controllers\Api\TenantApiController::class);

    // ─── Customer (Brands & Branches) ──────────────────────────────
    Route::apiResource('brands', \Modules\Customer\Http\Controllers\Api\BrandApiController::class);
    Route::apiResource('branches', \Modules\Customer\Http\Controllers\Api\BranchApiController::class);

    // ─── Email / Mailing ──────────────────────────────────────────
    Route::prefix('email-campaigns')->name('email-campaigns.')->group(function () {
        Route::get('/', [\Modules\Email\Http\Controllers\Api\EmailCampaignApiController::class, 'index'])->name('index');
        Route::post('/', [\Modules\Email\Http\Controllers\Api\EmailCampaignApiController::class, 'store'])->name('store');
        Route::delete('/{id}', [\Modules\Email\Http\Controllers\Api\EmailCampaignApiController::class, 'destroy'])->name('destroy');
    });
    Route::apiResource('email-templates', \Modules\Email\Http\Controllers\Api\EmailTemplateApiController::class);
    Route::apiResource('email-credentials', \Modules\Email\Http\Controllers\Api\EmailCredentialApiController::class);
    Route::apiResource('email-recipients', \Modules\Email\Http\Controllers\Api\EmailRecipientApiController::class);
    Route::apiResource('email-groups', \Modules\Email\Http\Controllers\Api\EmailGroupApiController::class);
    Route::apiResource('email-subscribers', \Modules\Email\Http\Controllers\Api\EmailSubscriberApiController::class);
    Route::apiResource('email-log', \Modules\Email\Http\Controllers\Api\EmailLogApiController::class)->only(['index', 'destroy']);
    Route::post('compose-email', [\Modules\Email\Http\Controllers\Api\ComposeEmailApiController::class, 'send'])->name('compose-email.send');

    // ─── Localization ─────────────────────────────────────────────
    Route::apiResource('languages', \Modules\Localization\Http\Controllers\Api\LanguageApiController::class);
    Route::apiResource('translations', \Modules\Localization\Http\Controllers\Api\TranslationApiController::class);

    // ─── Geography ────────────────────────────────────────────────
    Route::apiResource('countries', \Modules\Geography\Http\Controllers\Api\CountryApiController::class);
    Route::apiResource('provinces', \Modules\Geography\Http\Controllers\Api\ProvinceApiController::class)->except(['show', 'update']);
    Route::apiResource('cities', \Modules\Geography\Http\Controllers\Api\CityApiController::class)->except(['show', 'update']);
    Route::apiResource('towns', \Modules\Geography\Http\Controllers\Api\TownApiController::class)->except(['show', 'update']);
    Route::apiResource('streets', \Modules\Geography\Http\Controllers\Api\StreetApiController::class)->except(['show', 'update']);

    // ─── Utilities ────────────────────────────────────────────────
    Route::apiResource('categories', \Modules\Utilities\Http\Controllers\Api\CategoryApiController::class);
    Route::apiResource('tags', \Modules\Utilities\Http\Controllers\Api\TagApiController::class)->except(['show', 'update']);
    Route::apiResource('types', \Modules\Utilities\Http\Controllers\Api\TypeApiController::class)->except(['show', 'update']);
    Route::apiResource('industries', \Modules\Utilities\Http\Controllers\Api\IndustryApiController::class)->except(['show', 'update']);
    Route::apiResource('currencies', \Modules\Utilities\Http\Controllers\Api\CurrencyApiController::class);
    Route::apiResource('units', \Modules\Utilities\Http\Controllers\Api\UnitApiController::class)->except(['show', 'update']);
    Route::apiResource('announcements', \Modules\Utilities\Http\Controllers\Api\AnnouncementApiController::class);
    Route::apiResource('static-pages', \Modules\StaticPages\Http\Controllers\Api\StaticPageApiController::class);
    Route::apiResource('releases', \Modules\Utilities\Http\Controllers\Api\ReleaseApiController::class)->except(['show', 'update']);

    // ─── Modules ──────────────────────────────────────────────────
    Route::get('modules', [\Modules\Utilities\Http\Controllers\Api\ModuleApiController::class, 'index'])->name('modules.index');
    Route::patch('modules/{id}', [\Modules\Utilities\Http\Controllers\Api\ModuleApiController::class, 'toggle'])->name('modules.toggle');

    // ─── Subscriptions & Plans ────────────────────────────────────
    Route::apiResource('plans', \Modules\Subscription\Http\Controllers\Api\PlanApiController::class);
    Route::apiResource('subscriptions', \Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class)->only(['index', 'destroy']);

    // ─── Payment Methods & Analytics ──────────────────────────────
    Route::apiResource('payment-methods', \Modules\Payment\Http\Controllers\Api\PaymentMethodApiController::class)->except(['show', 'update']);
    Route::get('payment-analytics', [\Modules\Payment\Http\Controllers\Api\PaymentAnalyticsApiController::class, 'index'])->name('payment-analytics.index');

    // ─── Development ──────────────────────────────────────────────
    Route::get('system-status', [\Modules\Development\Http\Controllers\Api\SystemStatusApiController::class, 'index'])->name('system-status.index');
    Route::apiResource('configurations', \Modules\Development\Http\Controllers\Api\ConfigurationApiController::class);
    Route::apiResource('backups', \Modules\Development\Http\Controllers\Api\BackupApiController::class)->except(['show', 'update']);
    Route::get('backups/{id}/download', [\Modules\Development\Http\Controllers\Api\BackupApiController::class, 'download'])->name('backups.download');
    Route::apiResource('feature-flags', \Modules\Development\Http\Controllers\Api\FeatureFlagApiController::class);
    Route::apiResource('ip-blacklists', \Modules\Development\Http\Controllers\Api\IpBlacklistApiController::class);
    Route::post('code-builder', [\Modules\Development\Http\Controllers\Api\CodeBuilderApiController::class, 'build'])->name('code-builder.build');
    Route::get('env-diff', [\Modules\Development\Http\Controllers\Api\EnvDiffApiController::class, 'index'])->name('env-diff.index');
    Route::get('entities', [\Modules\Development\Http\Controllers\Api\ModuleEntityApiController::class, 'index'])->name('entities.index');
    Route::post('module-entities', [\Modules\Development\Http\Controllers\Api\ModuleEntityApiController::class, 'sync'])->name('entities.sync');
    Route::get('module-entities-map', [\Modules\Development\Http\Controllers\Api\ModuleEntityApiController::class, 'map'])->name('entities.map');

    // ─── Monitoring ───────────────────────────────────────────────
    Route::get('monitoring', [\Modules\Development\Http\Controllers\Api\MonitoringApiController::class, 'index'])->name('monitoring.index');
    Route::get('system-health', [\Modules\Development\Http\Controllers\Api\SystemHealthApiController::class, 'index'])->name('system-health.index');
    Route::get('tenant-monitoring', [\Modules\Development\Http\Controllers\Api\TenantMonitoringApiController::class, 'index'])->name('tenant-monitoring.index');

    // ─── Tickets ──────────────────────────────────────────────────
    Route::apiResource('tickets', \Modules\Ticket\Http\Controllers\Api\TicketApiController::class);

    // ─── Documentation ────────────────────────────────────────────
    Route::apiResource('documentation', \Modules\Development\Http\Controllers\Api\DocumentationApiController::class);

    // ─── File Manager ─────────────────────────────────────────────
    Route::get('file-manager', [\Modules\FileManager\Http\Controllers\Api\FileManagerApiController::class, 'index'])->name('file-manager.index');
    Route::apiResource('files', \Modules\FileManager\Http\Controllers\Api\FileApiController::class)->except(['show', 'update']);

    // ─── Permission Groups ───────────────────────────────────────
    Route::apiResource('permission-groups', \Modules\Auth\Http\Controllers\Landlord\PermissionGroupApiController::class);

    // ─── Onboarding ───────────────────────────────────────────────
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::post('select-plan', [\Modules\Subscription\Http\Controllers\Api\OnboardingApiController::class, 'selectPlan'])->name('select-plan');
        Route::post('select-modules', [\Modules\Subscription\Http\Controllers\Api\OnboardingApiController::class, 'selectModules'])->name('select-modules');
        Route::post('create-brand', [\Modules\Customer\Http\Controllers\Api\OnboardingApiController::class, 'createBrand'])->name('create-brand');
    });
});
