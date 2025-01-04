<?php

namespace Modules\Localization\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Localization\Repositories\LanguageInterface;
use Modules\Localization\Repositories\LanguageRepository;
use Modules\Localization\Repositories\TranslationInterface;
use Modules\Localization\Repositories\TranslationRepository;

class LocalizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LanguageInterface::class, LanguageRepository::class);
        $this->app->bind(TranslationInterface::class, TranslationRepository::class);
    }

    public function boot() {}
}
