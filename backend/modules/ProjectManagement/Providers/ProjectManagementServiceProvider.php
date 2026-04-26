<?php

namespace Modules\ProjectManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProjectManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'ProjectManagement';

    protected string $nameLower = 'projectmanagement';

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

        $this->registerStrategies();
        $this->registerRepositories();
    }

    /**
     * Register repository bindings.
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentProjectRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentTaskRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\WorkspaceRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentWorkspaceRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\MilestoneRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentMilestoneRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\BoardColumnRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentBoardColumnRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\BoardSwimlaneRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentBoardSwimlaneRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\CommentRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentCommentRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\IssueRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentIssueRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\ProjectMemberRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentProjectMemberRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\RiskRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentRiskRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\ProjectTemplateRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentProjectTemplateRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\WebhookRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentWebhookRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\SprintCycleRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentSprintCycleRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\TaskDependencyRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentTaskDependencyRepository::class
        );

        $this->app->bind(
            \Modules\ProjectManagement\Infrastructure\Persistence\LabelRepositoryInterface::class,
            \Modules\ProjectManagement\Infrastructure\Persistence\EloquentLabelRepository::class
        );
    }

    /**
     * Register strategy bindings.
     */
    protected function registerStrategies(): void
    {
        // Task Assignment Strategy
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\TaskAssignment\TaskAssignmentStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\TaskAssignment\DefaultTaskAssignmentStrategy::class
        );

        // Scheduling Strategy
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\Scheduling\SchedulingStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\Scheduling\DefaultSchedulingStrategy::class
        );

        // Project Health Strategy
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\ProjectHealth\ProjectHealthStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\ProjectHealth\DefaultProjectHealthStrategy::class
        );

        // Notification Strategy
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\Notification\NotificationStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\Notification\DefaultNotificationStrategy::class
        );

        // Automation Action Strategy
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\AutomationAction\AutomationActionStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\AutomationAction\DefaultAutomationActionStrategy::class
        );

        // Board Column Strategy (WIP limits)
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\BoardColumn\BoardColumnStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\BoardColumn\DefaultBoardColumnStrategy::class
        );

        // Task Position Strategy (drag-and-drop reordering)
        $this->app->bind(
            \Modules\ProjectManagement\Domain\Strategies\TaskPosition\TaskPositionStrategyInterface::class,
            \Modules\ProjectManagement\Domain\Strategies\TaskPosition\DefaultTaskPositionStrategy::class
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
