<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Repositories\ActivityLogInterface;
use Modules\Auth\Repositories\ActivityLogRepository;
use Modules\Auth\Repositories\PermissionInterface;
use Modules\Auth\Repositories\PermissionRepository;
use Modules\Auth\Repositories\PermissionGroupInterface;
use Modules\Auth\Repositories\PermissionGroupRepository;
use Modules\Auth\Repositories\RoleRepository;
use Modules\Auth\Repositories\RoleInterface;
use Modules\Auth\Repository\UserManagementRepositoryInterface;
use Modules\Auth\Repository\UserManagementRepository;
use Modules\Auth\Repository\RolePermissionRepositoryInterface;
use Modules\Auth\Repository\RolePermissionRepository;
use Modules\Auth\Services\ProfileServiceInterface;
use Modules\Auth\Services\ProfileService;
use Modules\Auth\Repositories\ProfileRepositoryInterface;
use Modules\Auth\Repositories\ProfileRepository;
use Modules\Auth\Services\SettingsServiceInterface;
use Modules\Auth\Services\SettingsService;
use Modules\Auth\Repositories\SettingsRepositoryInterface;
use Modules\Auth\Repositories\SettingsRepository;
use Modules\Auth\Services\Tenant\TenantRoleServiceInterface;
use Modules\Auth\Services\Tenant\TenantRoleService;
use Modules\Auth\Repositories\Tenant\TenantRoleRepositoryInterface;
use Modules\Auth\Repositories\Tenant\TenantRoleRepository;
use Modules\Auth\Services\Tenant\TenantPermissionServiceInterface;
use Modules\Auth\Services\Tenant\TenantPermissionService;
use Modules\Auth\Repositories\Tenant\TenantPermissionRepositoryInterface;
use Modules\Auth\Repositories\Tenant\TenantPermissionRepository;
use Modules\Auth\Services\Tenant\TenantUserManagementServiceInterface;
use Modules\Auth\Services\Tenant\TenantUserManagementService;
use Modules\Auth\Repositories\Tenant\TenantUserManagementRepositoryInterface;
use Modules\Auth\Repositories\Tenant\TenantUserManagementRepository;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(PermissionGroupInterface::class, PermissionGroupRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(ActivityLogInterface::class, ActivityLogRepository::class);
        $this->app->bind(UserManagementRepositoryInterface::class, UserManagementRepository::class);
        $this->app->bind(RolePermissionRepositoryInterface::class, RolePermissionRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(SettingsServiceInterface::class, SettingsService::class);

        // Tenant Role Management Bindings
        $this->app->bind(TenantRoleRepositoryInterface::class, TenantRoleRepository::class);
        $this->app->bind(TenantRoleServiceInterface::class, TenantRoleService::class);

        // Tenant Permission Management Bindings
        $this->app->bind(TenantPermissionRepositoryInterface::class, TenantPermissionRepository::class);
        $this->app->bind(TenantPermissionServiceInterface::class, TenantPermissionService::class);

        // Tenant User Management Bindings
        $this->app->bind(TenantUserManagementRepositoryInterface::class, TenantUserManagementRepository::class);
        $this->app->bind(TenantUserManagementServiceInterface::class, TenantUserManagementService::class);
    }

    public function boot() {}
}
