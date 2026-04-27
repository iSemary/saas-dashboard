<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use App\Repositories\WebhookRepositoryInterface;
use App\Repositories\WebhookRepository;
use App\Repositories\BackupRepositoryInterface;
use App\Repositories\BackupRepository;
use App\Repositories\ReportRepositoryInterface;
use App\Repositories\ReportRepository;
use App\Repositories\ImportExportRepositoryInterface;
use App\Repositories\ImportExportRepository;
use App\Repositories\TicketRepositoryInterface;
use App\Repositories\TicketRepository;
use App\Repositories\CrossDb\LandlordRepositoryInterface;
use App\Repositories\CrossDb\LandlordRepository;
use App\Repositories\CrossDb\TenantRepositoryInterface;
use App\Repositories\CrossDb\TenantRepository;
use Modules\Tenant\Repository\TenantOwnerRepositoryInterface;
use Modules\Tenant\Repository\TenantOwnerRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantOwner repository binding as fallback
        $this->app->bind(TenantOwnerRepositoryInterface::class, TenantOwnerRepository::class);

        // App-level Repository bindings
        $this->app->bind(WebhookRepositoryInterface::class, WebhookRepository::class);
        $this->app->bind(BackupRepositoryInterface::class, BackupRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(ImportExportRepositoryInterface::class, ImportExportRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(LandlordRepositoryInterface::class, LandlordRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
    }

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

        // Tenant-aware asset URL directive
        Blade::directive('tenantAsset', function ($expression) {
            return "<?php echo request()->getSchemeAndHttpHost() . '/assets/' . ltrim($expression, '/'); ?>";
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
