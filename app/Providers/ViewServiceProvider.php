<?php

namespace App\Providers;

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
            $notificationsCount = auth()->user()->notifications()->whereNull('seen_at')->count();
            $notifications = auth()->user()->notifications()->orderByDesc("id")->take(10)->get();
            
            $language = auth()->user()->language;

            $view->with('language', $language);
            $view->with('notifications', $notifications);
            $view->with('notificationsCount', $notificationsCount);
        });
    }
}
