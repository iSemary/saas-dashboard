<?php

namespace Modules\Geography\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Geography\Repositories\CountryInterface;
use Modules\Geography\Repositories\CountryRepository;

class GeographyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CountryInterface::class, CountryRepository::class);
    }

    public function boot() {}
}
