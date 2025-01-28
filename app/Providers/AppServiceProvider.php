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
        Blade::directive('translate', function ($expression, $attributes = [], $locale = null) {
            return $this->generateTranslationCode($expression, $attributes, $locale);
        });

        // Translate dashboard content global function (short version)
        Blade::directive('t', function ($expression, $attributes = [], $locale = null) {
            return $this->generateTranslationCode($expression, $attributes, $locale);
        });

        // Get configuration function from DB or Cache
        Blade::directive('configuration', function ($expression) {
            return "<?php echo configuration($expression); ?>";
        });

        // if app is production then it will prohibits these commands:
        // db:wipe, migrate:refresh, migrate:fresh, migrate:reset
        DB::prohibitDestructiveCommands((env("APP_ENV") == "production"));


        // Customized the Route::resource method to add restore method
        $registrar = new \App\Registrar\ResourceRegistrar($this->app['router']);
        $this->app->bind('Illuminate\Routing\ResourceRegistrar', function () use ($registrar) {
            return $registrar;
        });
    }

    // Helper function to generate the PHP code for translation directives
    private function generateTranslationCode($expression, $attributes = [], $locale = null)
    {
        // Serialize the attributes array to a JSON string
        $attributesJson = json_encode($attributes);
        // Return the PHP code to be executed
        return "<?php echo translate($expression, $attributesJson, $locale); ?>";
    }
}
