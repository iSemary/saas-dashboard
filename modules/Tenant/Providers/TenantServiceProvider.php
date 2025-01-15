<?php

namespace Modules\Tenant\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tenant\Repositories\SystemUserInterface;
use Modules\Tenant\Repositories\SystemUserRepository;
use Modules\Tenant\Repositories\TenantInterface;
use Modules\Tenant\Repositories\TenantRepository;

class TenantServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TenantInterface::class, TenantRepository::class);
        $this->app->bind(SystemUserInterface::class, SystemUserRepository::class);
    }

    public function boot() {}
}
