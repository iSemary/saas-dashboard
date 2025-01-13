<?php

namespace Modules\Utilities\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Utilities\Repositories\CurrencyInterface;
use Modules\Utilities\Repositories\CurrencyRepository;
use Modules\Utilities\Repositories\CategoryInterface;
use Modules\Utilities\Repositories\CategoryRepository;
use Modules\Utilities\Repositories\TagInterface;
use Modules\Utilities\Repositories\TagRepository;
use Modules\Utilities\Repositories\AnnouncementInterface;
use Modules\Utilities\Repositories\AnnouncementRepository;
use Modules\Utilities\Repositories\ReleaseInterface;
use Modules\Utilities\Repositories\ReleaseRepository;
use Modules\Utilities\Repositories\ModuleInterface;
use Modules\Utilities\Repositories\ModuleRepository;
use Modules\Utilities\Repositories\TypeInterface;
use Modules\Utilities\Repositories\TypeRepository;
use Modules\Utilities\Repositories\IndustryInterface;
use Modules\Utilities\Repositories\IndustryRepository;

class UtilitiesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CurrencyInterface::class, CurrencyRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(TagInterface::class, TagRepository::class);
        $this->app->bind(AnnouncementInterface::class, AnnouncementRepository::class);
        $this->app->bind(ModuleInterface::class, ModuleRepository::class);
        $this->app->bind(TypeInterface::class, TypeRepository::class);
        $this->app->bind(IndustryInterface::class, IndustryRepository::class);
        $this->app->bind(ReleaseInterface::class, ReleaseRepository::class);
    }

    public function boot() {}
}
