<?php

namespace Modules\Utilities\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Utilities\Repositories\CurrencyInterface;
use Modules\Utilities\Repositories\CurrencyRepository;
use Modules\Utilities\Repositories\CategoryInterface;
use Modules\Utilities\Repositories\CategoryRepository;

class UtilitiesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CurrencyInterface::class, CurrencyRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
    }

    public function boot() {}
}
