<?php
declare(strict_types=1);
namespace Modules\Expenses\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use Modules\Expenses\Providers\EventServiceProvider;
use Modules\Expenses\Infrastructure\Persistence\ExpenseCategoryRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentExpenseCategoryRepository;
use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentExpenseRepository;
use Modules\Expenses\Infrastructure\Persistence\ExpenseReportRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentExpenseReportRepository;
use Modules\Expenses\Infrastructure\Persistence\ExpensePolicyRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentExpensePolicyRepository;
use Modules\Expenses\Infrastructure\Persistence\ExpenseTagRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentExpenseTagRepository;
use Modules\Expenses\Infrastructure\Persistence\ReimbursementRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\EloquentReimbursementRepository;
use Modules\Expenses\Domain\Strategies\ExpenseApproval\ExpenseApprovalStrategyInterface;
use Modules\Expenses\Domain\Strategies\ExpenseApproval\DefaultExpenseApprovalStrategy;
use Modules\Expenses\Domain\Strategies\ReimbursementProcessing\ReimbursementProcessingStrategyInterface;
use Modules\Expenses\Domain\Strategies\ReimbursementProcessing\DefaultReimbursementProcessingStrategy;
use Modules\Expenses\Domain\Strategies\ReceiptProcessing\ReceiptProcessingStrategyInterface;
use Modules\Expenses\Domain\Strategies\ReceiptProcessing\DefaultReceiptProcessingStrategy;
use Modules\Expenses\Domain\Strategies\PolicyValidation\PolicyValidationStrategyInterface;
use Modules\Expenses\Domain\Strategies\PolicyValidation\DefaultPolicyValidationStrategy;

class ExpensesServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Expenses';
    protected string $nameLower = 'expenses';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations/tenant'));
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);

        // Repository bindings
        $this->app->bind(ExpenseCategoryRepositoryInterface::class, EloquentExpenseCategoryRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, EloquentExpenseRepository::class);
        $this->app->bind(ExpenseReportRepositoryInterface::class, EloquentExpenseReportRepository::class);
        $this->app->bind(ExpensePolicyRepositoryInterface::class, EloquentExpensePolicyRepository::class);
        $this->app->bind(ExpenseTagRepositoryInterface::class, EloquentExpenseTagRepository::class);
        $this->app->bind(ReimbursementRepositoryInterface::class, EloquentReimbursementRepository::class);

        // Strategy bindings
        $this->app->bind(ExpenseApprovalStrategyInterface::class, DefaultExpenseApprovalStrategy::class);
        $this->app->bind(ReimbursementProcessingStrategyInterface::class, DefaultReimbursementProcessingStrategy::class);
        $this->app->bind(ReceiptProcessingStrategyInterface::class, DefaultReceiptProcessingStrategy::class);
        $this->app->bind(PolicyValidationStrategyInterface::class, DefaultPolicyValidationStrategy::class);
    }

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

    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');
        if (is_dir($sourcePath)) {
            $this->publishes([$sourcePath => $viewPath], 'views');
            $this->loadViewsFrom(array_merge($this->app['config']->get('view.paths', []), [$sourcePath]), $this->nameLower);
        }
    }
}
