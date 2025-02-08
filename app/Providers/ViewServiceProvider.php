<?php

namespace App\Providers;

use App\Helpers\TranslateHelper;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $userId = auth()->id();
            $cacheTime = now()->addMinutes(config('notifications.cache_duration', 30));

            $notificationsCount = cache()->remember("user.{$userId}.notifications.count", $cacheTime, function () {
                return auth()->user()->notifications()->whereNull('seen_at')->count();
            });

            $language = TranslateHelper::getLanguage();
            Carbon::setLocale($language->locale);

            $view->with('language', $language);
            $view->with('notificationsCount', $notificationsCount);
        });
    }
}
