<?php

namespace Modules\EmailMarketing\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class EmailMarketingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'EmailMarketing';

    protected string $nameLower = 'email_marketing';

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
            \Modules\EmailMarketing\Infrastructure\Persistence\EmCampaignRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmCampaignRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmTemplateRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmTemplateRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmContactRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmContactRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmContactListRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmContactListRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmSendingLogRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmSendingLogRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmCredentialRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmCredentialRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmAutomationRuleRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmAutomationRuleRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmWebhookRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmWebhookRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmAbTestRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmAbTestRepository::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Infrastructure\Persistence\EmImportJobRepositoryInterface::class,
            \Modules\EmailMarketing\Infrastructure\Persistence\EloquentEmImportJobRepository::class
        );
    }

    protected function registerStrategies(): void
    {
        $this->app->bind(
            \Modules\EmailMarketing\Domain\Strategies\Sending\EmailSendingStrategyInterface::class,
            \Modules\EmailMarketing\Domain\Strategies\Sending\LogEmailSendStrategy::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Domain\Strategies\Import\EmailImportStrategyInterface::class,
            \Modules\EmailMarketing\Domain\Strategies\Import\CsvEmailImportStrategy::class
        );

        $this->app->bind(
            \Modules\EmailMarketing\Domain\Strategies\Automation\EmailAutomationActionInterface::class,
            \Modules\EmailMarketing\Domain\Strategies\Automation\DefaultEmailAutomationAction::class
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
