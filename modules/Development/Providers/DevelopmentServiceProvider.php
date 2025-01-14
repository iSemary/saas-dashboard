<?php

namespace Modules\Development\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Development\Repositories\ConfigurationInterface;
use Modules\Development\Repositories\ConfigurationRepository;

class DevelopmentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ConfigurationInterface::class, ConfigurationRepository::class);
    }

    public function boot() {}
}
