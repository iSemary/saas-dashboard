<?php

namespace Modules\Utilities\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Utilities\Repositories\CurrencyInterface;
use Modules\Utilities\Repositories\CurrencyRepository;

class UtilitiesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CurrencyInterface::class, CurrencyRepository::class);
    }

    public function boot() {}
}
