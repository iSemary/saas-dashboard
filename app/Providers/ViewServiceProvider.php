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
            $notifications = auth()->user()->notifications()->latest()->take(10)->get();
            $notificationsCount = auth()->user()->notifications()->whereNull('seen_at')->count();
            
            $view->with('notifications', $notifications);
            $view->with('notificationsCount', $notificationsCount);
        });
    }
}
