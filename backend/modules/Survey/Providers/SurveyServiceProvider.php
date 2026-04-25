<?php

namespace Modules\Survey\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SurveyServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Survey';

    protected string $nameLower = 'survey';

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
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        // $this->app->register(RouteServiceProvider::class);

        $this->registerStrategies();
        $this->registerRepositories();
    }

    /**
     * Register repository bindings.
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyPageRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyPageRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyQuestionRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyResponseRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyAnswerRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyAnswerRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyTemplateRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyTemplateRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyThemeRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyThemeRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyAutomationRuleRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyAutomationRuleRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyWebhookRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyWebhookRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyShareRepository::class
        );

        $this->app->bind(
            \Modules\Survey\Infrastructure\Persistence\SurveyQuestionOptionRepositoryInterface::class,
            \Modules\Survey\Infrastructure\Persistence\EloquentSurveyQuestionOptionRepository::class
        );
    }

    /**
     * Register strategy bindings.
     */
    protected function registerStrategies(): void
    {
        // Question Type Strategy
        $this->app->bind(
            \Modules\Survey\Domain\Strategies\QuestionType\QuestionTypeStrategyInterface::class,
            \Modules\Survey\Domain\Strategies\QuestionType\DefaultQuestionTypeStrategy::class
        );

        // Branching Strategy
        $this->app->bind(
            \Modules\Survey\Domain\Strategies\Branching\BranchingStrategyInterface::class,
            \Modules\Survey\Domain\Strategies\Branching\DefaultBranchingStrategy::class
        );

        // Scoring Strategy
        $this->app->bind(
            \Modules\Survey\Domain\Strategies\Scoring\ScoringStrategyInterface::class,
            \Modules\Survey\Domain\Strategies\Scoring\DefaultScoringStrategy::class
        );

        // Distribution Strategies (tagged collection)
        $this->app->tag([
            \Modules\Survey\Domain\Strategies\Distribution\EmailDistributionStrategy::class,
            \Modules\Survey\Domain\Strategies\Distribution\LinkDistributionStrategy::class,
            \Modules\Survey\Domain\Strategies\Distribution\EmbedDistributionStrategy::class,
            \Modules\Survey\Domain\Strategies\Distribution\SmsDistributionStrategy::class,
            \Modules\Survey\Domain\Strategies\Distribution\QrCodeDistributionStrategy::class,
            \Modules\Survey\Domain\Strategies\Distribution\SocialDistributionStrategy::class,
        ], 'survey.distribution_strategies');

        // Notification Strategies (tagged collection)
        $this->app->tag([
            \Modules\Survey\Domain\Strategies\Notification\EmailNotificationStrategy::class,
            \Modules\Survey\Domain\Strategies\Notification\SmsNotificationStrategy::class,
            \Modules\Survey\Domain\Strategies\Notification\PushNotificationStrategy::class,
        ], 'survey.notification_strategies');

        // AI Generation Strategy
        $this->app->bind(
            \Modules\Survey\Domain\Strategies\AiGeneration\AiGenerationStrategyInterface::class,
            \Modules\Survey\Domain\Strategies\AiGeneration\OpenAiGenerationStrategy::class
        );

        // Piping Strategy
        $this->app->bind(
            \Modules\Survey\Domain\Strategies\Piping\PipingStrategyInterface::class,
            \Modules\Survey\Domain\Strategies\Piping\DefaultPipingStrategy::class
        );
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
