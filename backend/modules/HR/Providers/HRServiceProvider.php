<?php

namespace Modules\HR\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class HRServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'HR';

    protected string $nameLower = 'hr';

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
        // Core HR
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\DepartmentRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\DepartmentRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\PositionRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\PositionRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EmployeeRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\EmployeeDocumentRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EmployeeDocumentRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\EmployeeContractRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EmployeeContractRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\EmploymentHistoryRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\EmploymentHistoryRepository::class);

        // Attendance
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\ShiftRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\ShiftRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\WorkScheduleRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\WorkScheduleRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AttendanceRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AttendanceRegularizationRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AttendanceRegularizationRepository::class);

        // Leave
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\LeaveTypeRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\LeaveTypeRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\LeavePolicyRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\LeavePolicyRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\LeaveBalanceRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\LeaveRequestRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\HolidayRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\HolidayRepository::class);

        // Payroll
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\PayrollRepository::class);

        // Performance
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\PerformanceCycleRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\PerformanceCycleRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\GoalRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\GoalRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\KeyResultRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\KeyResultRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\PerformanceReviewRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\PerformanceReviewRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\OneOnOneRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\OneOnOneRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\FeedbackRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\FeedbackRepository::class);

        // Recruitment
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\JobOpeningRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\JobOpeningRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\RecruitmentPipelineStageRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\RecruitmentPipelineStageRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\CandidateRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\CandidateRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\ApplicationRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\InterviewRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\InterviewRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\OfferRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\OfferRepository::class);

        // Onboarding
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\OnboardingTemplateRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\OnboardingTemplateRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\OnboardingProcessRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\OnboardingProcessRepository::class);

        // Training
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\CourseRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\CourseRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\CourseEnrollmentRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\CourseEnrollmentRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\CertificationRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\CertificationRepository::class);

        // Assets
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AssetCategoryRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AssetCategoryRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AssetRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AssetRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AssetAssignmentRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AssetAssignmentRepository::class);

        // Expenses
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\ExpenseCategoryRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\ExpenseCategoryRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\ExpenseClaimRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\ExpenseClaimRepository::class);

        // Announcements
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\AnnouncementRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\AnnouncementRepository::class);
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\PolicyRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\PolicyRepository::class);

        // Dashboard
        $this->app->bind(\Modules\HR\Infrastructure\Persistence\HrDashboardRepositoryInterface::class, \Modules\HR\Infrastructure\Persistence\HrDashboardRepository::class);
    }

    /**
     * Register strategy bindings.
     */
    protected function registerStrategies(): void
    {
        // Leave Accrual Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\LeaveAccrual\LeaveAccrualStrategyInterface::class, \Modules\HR\Domain\Strategies\LeaveAccrual\NoAccrualStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\LeaveAccrual\NoAccrualStrategy::class,
            \Modules\HR\Domain\Strategies\LeaveAccrual\AnnualFixedStrategy::class,
            \Modules\HR\Domain\Strategies\LeaveAccrual\MonthlyAccrualStrategy::class,
            \Modules\HR\Domain\Strategies\LeaveAccrual\TenureBasedStrategy::class,
        ], 'hr.leave_accrual_strategies');

        // Leave Approval Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\LeaveApproval\LeaveApprovalStrategyInterface::class, \Modules\HR\Domain\Strategies\LeaveApproval\SingleApproverStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\LeaveApproval\SingleApproverStrategy::class,
            \Modules\HR\Domain\Strategies\LeaveApproval\MultiStepApprovalStrategy::class,
            \Modules\HR\Domain\Strategies\LeaveApproval\AutoApproveStrategy::class,
        ], 'hr.leave_approval_strategies');

        // Attendance Rule Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\AttendanceRule\AttendanceRuleStrategyInterface::class, \Modules\HR\Domain\Strategies\AttendanceRule\StandardScheduleStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\AttendanceRule\StandardScheduleStrategy::class,
            \Modules\HR\Domain\Strategies\AttendanceRule\FlexibleHoursStrategy::class,
            \Modules\HR\Domain\Strategies\AttendanceRule\ShiftBasedStrategy::class,
            \Modules\HR\Domain\Strategies\AttendanceRule\RemoteStrategy::class,
        ], 'hr.attendance_rule_strategies');

        // Overtime Calculation Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\OvertimeCalculation\OvertimeCalculationStrategyInterface::class, \Modules\HR\Domain\Strategies\OvertimeCalculation\Standard8HourStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\OvertimeCalculation\Standard8HourStrategy::class,
            \Modules\HR\Domain\Strategies\OvertimeCalculation\WeeklyThresholdStrategy::class,
            \Modules\HR\Domain\Strategies\OvertimeCalculation\ShiftBasedStrategy::class,
        ], 'hr.overtime_strategies');

        // Payroll Calculation Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\PayrollCalculation\PayrollCalculationStrategyInterface::class, \Modules\HR\Domain\Strategies\PayrollCalculation\SalariedStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\PayrollCalculation\SalariedStrategy::class,
            \Modules\HR\Domain\Strategies\PayrollCalculation\HourlyStrategy::class,
            \Modules\HR\Domain\Strategies\PayrollCalculation\CommissionStrategy::class,
        ], 'hr.payroll_calc_strategies');

        // Payslip Export Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\PayslipExport\PayslipExportStrategyInterface::class, \Modules\HR\Domain\Strategies\PayslipExport\PdfPayslipStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\PayslipExport\PdfPayslipStrategy::class,
            \Modules\HR\Domain\Strategies\PayslipExport\HtmlPayslipStrategy::class,
        ], 'hr.payslip_export_strategies');

        // Recruitment Pipeline Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\RecruitmentPipeline\RecruitmentPipelineStrategyInterface::class, \Modules\HR\Domain\Strategies\RecruitmentPipeline\LinearStageStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\RecruitmentPipeline\LinearStageStrategy::class,
            \Modules\HR\Domain\Strategies\RecruitmentPipeline\FlexibleStageStrategy::class,
        ], 'hr.recruitment_pipeline_strategies');

        // Onboarding Assignment Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\OnboardingAssignment\OnboardingAssignmentStrategyInterface::class, \Modules\HR\Domain\Strategies\OnboardingAssignment\RoleBasedAssignmentStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\OnboardingAssignment\RoleBasedAssignmentStrategy::class,
            \Modules\HR\Domain\Strategies\OnboardingAssignment\ManagerAssignmentStrategy::class,
        ], 'hr.onboarding_assignment_strategies');

        // Notification Channel Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\NotificationChannel\NotificationChannelStrategyInterface::class, \Modules\HR\Domain\Strategies\NotificationChannel\InAppNotificationStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\NotificationChannel\InAppNotificationStrategy::class,
            \Modules\HR\Domain\Strategies\NotificationChannel\EmailNotificationStrategy::class,
            \Modules\HR\Domain\Strategies\NotificationChannel\SmsNotificationStrategy::class,
        ], 'hr.notification_strategies');

        // Document Expiry Reminder Strategies
        $this->app->bind(\Modules\HR\Domain\Strategies\DocumentExpiryReminder\DocumentExpiryReminderStrategyInterface::class, \Modules\HR\Domain\Strategies\DocumentExpiryReminder\DaysBeforeStrategy::class);
        $this->app->tag([
            \Modules\HR\Domain\Strategies\DocumentExpiryReminder\DaysBeforeStrategy::class,
            \Modules\HR\Domain\Strategies\DocumentExpiryReminder\ProgressiveReminderStrategy::class,
        ], 'hr.document_reminder_strategies');
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
