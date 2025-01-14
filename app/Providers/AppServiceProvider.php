<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Translate dashboard content global function
        Blade::directive('translate', function ($expression) {
            return "<?php echo translate($expression); ?>";
        });

        // Translate dashboard content global function
        Blade::directive('configuration', function ($expression) {
            return "<?php echo configuration($expression); ?>";
        });

        // if app is production then it will prohibits these commands:
        // db:wipe, migrate:refresh, migrate:fresh, migrate:reset
        DB::prohibitDestructiveCommands((env("APP_ENV") == "production"));
    }
}
