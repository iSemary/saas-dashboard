<?php

namespace Modules\TimeManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TimeManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'TimeManagement';

    protected string $nameLower = 'timemanagement';

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
            \Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentTimeEntryRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentTimesheetRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\CalendarEventRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentCalendarEventRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\AttendanceRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentAttendanceRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\OvertimeRequestRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentOvertimeRequestRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\TimeSessionRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentTimeSessionRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\CalendarTokenRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentCalendarTokenRepository::class
        );

        $this->app->bind(
            \Modules\TimeManagement\Infrastructure\Persistence\WebhookRepositoryInterface::class,
            \Modules\TimeManagement\Infrastructure\Persistence\EloquentWebhookRepository::class
        );
    }

    /**
     * Register strategy bindings.
     */
    protected function registerStrategies(): void
    {
        // Time Validation Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\TimeValidation\TimeValidationStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\TimeValidation\DefaultTimeValidationStrategy::class
        );

        // Approval Routing Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\ApprovalRouting\ApprovalRoutingStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\ApprovalRouting\DefaultApprovalRoutingStrategy::class
        );

        // Utilization Calculation Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\UtilizationCalculation\UtilizationCalculationStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\UtilizationCalculation\DefaultUtilizationCalculationStrategy::class
        );

        // Reminder Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\Reminder\ReminderStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\Reminder\DefaultReminderStrategy::class
        );

        // Sync Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\Sync\SyncStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\Sync\DefaultSyncStrategy::class
        );

        // Calendar Sync Strategies (tagged collection)
        $this->app->tag([
            \Modules\TimeManagement\Domain\Strategies\CalendarSync\GoogleCalendarSyncStrategy::class,
            \Modules\TimeManagement\Domain\Strategies\CalendarSync\OutlookCalendarSyncStrategy::class,
        ], 'timemanagement.calendar_sync_strategies');

        // Meeting Link Strategies (tagged collection)
        $this->app->tag([
            \Modules\TimeManagement\Domain\Strategies\MeetingLink\GoogleMeetLinkStrategy::class,
            \Modules\TimeManagement\Domain\Strategies\MeetingLink\MicrosoftTeamsLinkStrategy::class,
            \Modules\TimeManagement\Domain\Strategies\MeetingLink\ZoomLinkStrategy::class,
        ], 'timemanagement.meeting_link_strategies');

        // Conflict Detection Strategy
        $this->app->bind(
            \Modules\TimeManagement\Domain\Strategies\ConflictDetection\ConflictDetectionStrategyInterface::class,
            \Modules\TimeManagement\Domain\Strategies\ConflictDetection\DefaultConflictDetectionStrategy::class
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
