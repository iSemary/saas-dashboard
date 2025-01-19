<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Repositories\ActivityLogInterface;
use Modules\Auth\Repositories\ActivityLogRepository;
use Modules\Auth\Repositories\PermissionInterface;
use Modules\Auth\Repositories\PermissionRepository;
use Modules\Auth\Repositories\RoleRepository;
use Modules\Auth\Repositories\RoleInterface;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(ActivityLogInterface::class, ActivityLogRepository::class);
    }

    public function boot() {}
}
