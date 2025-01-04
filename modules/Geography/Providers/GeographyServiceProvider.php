<?php

namespace Modules\Geography\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Geography\Repositories\CityInterface;
use Modules\Geography\Repositories\CityRepository;
use Modules\Geography\Repositories\CountryInterface;
use Modules\Geography\Repositories\CountryRepository;
use Modules\Geography\Repositories\ProvinceInterface;
use Modules\Geography\Repositories\ProvinceRepository;
use Modules\Geography\Repositories\StreetInterface;
use Modules\Geography\Repositories\StreetRepository;
use Modules\Geography\Repositories\TownInterface;
use Modules\Geography\Repositories\TownRepository;

class GeographyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CountryInterface::class, CountryRepository::class);
        $this->app->bind(ProvinceInterface::class, ProvinceRepository::class);
        $this->app->bind(CityInterface::class, CityRepository::class);
        $this->app->bind(TownInterface::class, TownRepository::class);
        $this->app->bind(StreetInterface::class, StreetRepository::class);
    }

    public function boot() {}
}
