<?php

namespace Modules\CRM\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CRMServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'CRM';

    protected string $nameLower = 'crm';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'Database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        // $this->app->register(RouteServiceProvider::class);

        $this->registerRepositories();
        $this->registerStrategies();
    }

    /**
     * Register repository bindings.
     */
    protected function registerRepositories(): void
    {
        // Legacy repository bindings
        $this->app->bind(\Modules\CRM\Repositories\CompanyRepositoryInterface::class, \Modules\CRM\Repositories\CompanyRepository::class);
        $this->app->bind(\Modules\CRM\Repositories\ContactRepositoryInterface::class, \Modules\CRM\Repositories\ContactRepository::class);
        $this->app->bind(\Modules\CRM\Repositories\CrmDashboardRepositoryInterface::class, \Modules\CRM\Repositories\CrmDashboardRepository::class);

        // DDD Infrastructure repository bindings
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\LeadRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\OpportunityRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\ActivityRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmNoteRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmNoteRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmFileRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmFileRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmPipelineStageRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmPipelineStageRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmAutomationRuleRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmAutomationRuleRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmWebhookRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmWebhookRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\CrmImportJobRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\CrmImportJobRepository::class);
        $this->app->bind(\Modules\CRM\Infrastructure\Persistence\AuditRepositoryInterface::class, \Modules\CRM\Infrastructure\Persistence\EloquentAuditRepository::class);
    }

    /**
     * Register strategy bindings.
     */
    protected function registerStrategies(): void
    {
        // Lead Qualification Strategies
        $this->app->bind(
            \Modules\CRM\Domain\Strategies\LeadQualification\LeadQualificationStrategyInterface::class,
            \Modules\CRM\Domain\Strategies\LeadQualification\BasicQualificationStrategy::class
        );

        // Pipeline Transition Strategies
        $this->app->bind(
            \Modules\CRM\Domain\Strategies\PipelineTransition\PipelineTransitionStrategyInterface::class,
            \Modules\CRM\Domain\Strategies\PipelineTransition\StrictTransitionStrategy::class
        );

        // Automation Action Strategies (tagged collection)
        $this->app->tag([
            \Modules\CRM\Domain\Strategies\AutomationAction\AssignUserAction::class,
            \Modules\CRM\Domain\Strategies\AutomationAction\UpdateFieldAction::class,
            \Modules\CRM\Domain\Strategies\AutomationAction\CreateActivityAction::class,
            \Modules\CRM\Domain\Strategies\AutomationAction\SendNotificationAction::class,
            \Modules\CRM\Domain\Strategies\AutomationAction\SendEmailAction::class,
        ], 'crm.automation_actions');

        // Import Strategies (tagged collection)
        $this->app->tag([
            \Modules\CRM\Domain\Strategies\Import\LeadImportStrategy::class,
            \Modules\CRM\Domain\Strategies\Import\ContactImportStrategy::class,
        ], 'crm.import_strategies');

        // Notification Strategies (tagged collection)
        $this->app->tag([
            \Modules\CRM\Domain\Strategies\Notification\EmailNotificationStrategy::class,
            \Modules\CRM\Domain\Strategies\Notification\SmsNotificationStrategy::class,
            \Modules\CRM\Domain\Strategies\Notification\PushNotificationStrategy::class,
        ], 'crm.notification_strategies');
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'Resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
