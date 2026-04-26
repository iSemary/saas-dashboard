<?php

namespace Modules\Accounting\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentChartOfAccountRepository;
use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentJournalEntryRepository;
use Modules\Accounting\Infrastructure\Persistence\JournalItemRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentJournalItemRepository;
use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentFiscalYearRepository;
use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentBudgetRepository;
use Modules\Accounting\Infrastructure\Persistence\BudgetItemRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentBudgetItemRepository;
use Modules\Accounting\Infrastructure\Persistence\TaxRateRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentTaxRateRepository;
use Modules\Accounting\Infrastructure\Persistence\BankAccountRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentBankAccountRepository;
use Modules\Accounting\Infrastructure\Persistence\BankTransactionRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentBankTransactionRepository;
use Modules\Accounting\Infrastructure\Persistence\ReconciliationRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\EloquentReconciliationRepository;
use Modules\Accounting\Domain\Strategies\JournalValidation\JournalValidationStrategyInterface;
use Modules\Accounting\Domain\Strategies\JournalValidation\DefaultJournalValidationStrategy;
use Modules\Accounting\Domain\Strategies\BalanceCalculation\BalanceCalculationStrategyInterface;
use Modules\Accounting\Domain\Strategies\BalanceCalculation\DefaultBalanceCalculationStrategy;
use Modules\Accounting\Domain\Strategies\ReportGeneration\ReportGenerationStrategyInterface;
use Modules\Accounting\Domain\Strategies\ReportGeneration\DefaultReportGenerationStrategy;

class AccountingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Accounting';

    protected string $nameLower = 'accounting';

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
        $this->app->bind(ChartOfAccountRepositoryInterface::class, EloquentChartOfAccountRepository::class);
        $this->app->bind(JournalEntryRepositoryInterface::class, EloquentJournalEntryRepository::class);
        $this->app->bind(JournalItemRepositoryInterface::class, EloquentJournalItemRepository::class);
        $this->app->bind(FiscalYearRepositoryInterface::class, EloquentFiscalYearRepository::class);
        $this->app->bind(BudgetRepositoryInterface::class, EloquentBudgetRepository::class);
        $this->app->bind(BudgetItemRepositoryInterface::class, EloquentBudgetItemRepository::class);
        $this->app->bind(TaxRateRepositoryInterface::class, EloquentTaxRateRepository::class);
        $this->app->bind(BankAccountRepositoryInterface::class, EloquentBankAccountRepository::class);
        $this->app->bind(BankTransactionRepositoryInterface::class, EloquentBankTransactionRepository::class);
        $this->app->bind(ReconciliationRepositoryInterface::class, EloquentReconciliationRepository::class);

        // Strategy bindings
        $this->app->bind(JournalValidationStrategyInterface::class, DefaultJournalValidationStrategy::class);
        $this->app->bind(BalanceCalculationStrategyInterface::class, DefaultBalanceCalculationStrategy::class);
        $this->app->bind(ReportGenerationStrategyInterface::class, DefaultReportGenerationStrategy::class);
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
