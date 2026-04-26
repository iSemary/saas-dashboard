<?php

namespace Modules\SmsMarketing\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SmsMarketingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'SmsMarketing';

    protected string $nameLower = 'sms_marketing';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);

        $this->registerStrategies();
        $this->registerRepositories();
    }

    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmCampaignRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmCampaignRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmTemplateRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmTemplateRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmContactRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmContactRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmContactListRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmContactListRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmSendingLogRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmSendingLogRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmCredentialRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmCredentialRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmAutomationRuleRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmAutomationRuleRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmWebhookRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmWebhookRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmAbTestRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmAbTestRepository::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Infrastructure\Persistence\SmImportJobRepositoryInterface::class,
            \Modules\SmsMarketing\Infrastructure\Persistence\EloquentSmImportJobRepository::class
        );
    }

    protected function registerStrategies(): void
    {
        $this->app->bind(
            \Modules\SmsMarketing\Domain\Strategies\Sending\SmsSendingStrategyInterface::class,
            \Modules\SmsMarketing\Domain\Strategies\Sending\LogSmsSendStrategy::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Domain\Strategies\Import\SmsImportStrategyInterface::class,
            \Modules\SmsMarketing\Domain\Strategies\Import\CsvSmsImportStrategy::class
        );

        $this->app->bind(
            \Modules\SmsMarketing\Domain\Strategies\Automation\SmsAutomationActionInterface::class,
            \Modules\SmsMarketing\Domain\Strategies\Automation\DefaultSmsAutomationAction::class
        );
    }

    protected function registerCommands(): void {}

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

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

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'Resources/views');
        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }
        return $paths;
    }
}
