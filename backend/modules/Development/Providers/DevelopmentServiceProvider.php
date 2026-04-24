<?php

namespace Modules\Development\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Development\Repositories\BackupInterface;
use Modules\Development\Repositories\BackupRepository;
use Modules\Development\Repositories\ConfigurationInterface;
use Modules\Development\Repositories\ConfigurationRepository;
use Modules\Development\Repositories\IpBlacklistInterface;
use Modules\Development\Repositories\IpBlacklistRepository;

class DevelopmentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ConfigurationInterface::class, ConfigurationRepository::class);
        $this->app->bind(IpBlacklistInterface::class, IpBlacklistRepository::class);
        $this->app->bind(BackupInterface::class, BackupRepository::class);
    }

    public function boot() {}
}
