# Accounting Module — Backend

## Directory Layout

```
backend/modules/Accounting/
├── Domain/
│   ├── Entities/
│   │   ├── ChartOfAccount.php          - Hierarchical account (parent_id, type, current_balance)
│   │   ├── JournalEntry.php            - Entry header (entry_number, state, total_debit/credit)
│   │   ├── JournalItem.php             - Line item (account_id, debit, credit)
│   │   ├── FiscalYear.php              - Period (start_date, end_date, is_active)
│   │   ├── Budget.php                  - Budget header (fiscal_year_id, department_id, status)
│   │   ├── BudgetItem.php              - Budget line (account_id, planned_amount, actual_amount)
│   │   ├── TaxRate.php                 - Tax config (name, rate, type, is_compound)
│   │   ├── BankAccount.php             - Bank details (bank_name, account_number, balance)
│   │   ├── BankTransaction.php         - Transaction (type, amount, date, reference)
│   │   └── Reconciliation.php         - Reconciliation (statement_date, statement_balance, status)
│   ├── ValueObjects/
│   │   ├── AccountType.php             - asset, liability, equity, income, expense
│   │   ├── AccountSubType.php          - Sub-categorization within account types
│   │   ├── JournalEntryState.php       - draft, posted, cancelled
│   │   ├── FiscalYearStatus.php        - open, closed, locked
│   │   ├── BudgetStatus.php            - draft, active, archived
│   │   ├── BankTransactionType.php     - deposit, withdrawal, transfer
│   │   └── ReconciliationStatus.php    - draft, in_progress, completed
│   ├── Events/
│   │   ├── JournalEntryCreated.php
│   │   ├── JournalEntryPosted.php
│   │   ├── FiscalYearCreated.php
│   │   └── BudgetCreated.php
│   ├── Exceptions/
│   │   ├── UnbalancedJournalEntry.php
│   │   ├── InvalidJournalEntryTransition.php
│   │   ├── InvalidFiscalYearTransition.php
│   │   └── FiscalYearClosed.php
│   └── Strategies/
│       ├── JournalValidation/
│       │   ├── JournalValidationStrategyInterface.php
│       │   └── DefaultJournalValidationStrategy.php
│       ├── BalanceCalculation/
│       │   ├── BalanceCalculationStrategyInterface.php
│       │   └── DefaultBalanceCalculationStrategy.php
│       └── ReportGeneration/
│           ├── ReportGenerationStrategyInterface.php
│           └── DefaultReportGenerationStrategy.php
├── Application/
│   ├── DTOs/
│   │   ├── CreateChartOfAccountData.php
│   │   ├── UpdateChartOfAccountData.php
│   │   ├── CreateJournalEntryData.php
│   │   ├── UpdateJournalEntryData.php
│   │   ├── CreateFiscalYearData.php
│   │   ├── UpdateFiscalYearData.php
│   │   ├── CreateBudgetData.php
│   │   ├── UpdateBudgetData.php
│   │   ├── CreateTaxRateData.php
│   │   ├── UpdateTaxRateData.php
│   │   ├── CreateBankAccountData.php
│   │   ├── UpdateBankAccountData.php
│   │   ├── CreateBankTransactionData.php
│   │   ├── UpdateBankTransactionData.php
│   │   ├── CreateReconciliationData.php
│   │   └── UpdateReconciliationData.php
│   └── UseCases/
│       ├── ChartOfAccountUseCase.php
│       ├── JournalEntryUseCase.php
│       ├── FiscalYearUseCase.php
│       ├── BudgetUseCase.php
│       ├── TaxRateUseCase.php
│       ├── BankAccountUseCase.php
│       ├── BankTransactionUseCase.php
│       ├── ReconciliationUseCase.php
│       ├── PostJournalEntry.php
│       ├── CancelJournalEntry.php
│       ├── CloseFiscalYear.php
│       └── GenerateReport.php
├── Infrastructure/
│   ├── Persistence/
│   │   ├── ChartOfAccountRepositoryInterface.php
│   │   ├── EloquentChartOfAccountRepository.php
│   │   ├── JournalEntryRepositoryInterface.php
│   │   ├── EloquentJournalEntryRepository.php
│   │   ├── JournalItemRepositoryInterface.php
│   │   ├── EloquentJournalItemRepository.php
│   │   ├── FiscalYearRepositoryInterface.php
│   │   ├── EloquentFiscalYearRepository.php
│   │   ├── BudgetRepositoryInterface.php
│   │   ├── EloquentBudgetRepository.php
│   │   ├── BudgetItemRepositoryInterface.php
│   │   ├── EloquentBudgetItemRepository.php
│   │   ├── TaxRateRepositoryInterface.php
│   │   ├── EloquentTaxRateRepository.php
│   │   ├── BankAccountRepositoryInterface.php
│   │   ├── EloquentBankAccountRepository.php
│   │   ├── BankTransactionRepositoryInterface.php
│   │   ├── EloquentBankTransactionRepository.php
│   │   ├── ReconciliationRepositoryInterface.php
│   │   └── EloquentReconciliationRepository.php
│   └── Listeners/
│       └── RecalculateAccountBalances.php
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/
│       │   ├── AccountingDashboardController.php
│       │   ├── ChartOfAccountController.php
│       │   ├── JournalEntryController.php
│       │   ├── FiscalYearController.php
│       │   ├── BudgetController.php
│       │   ├── TaxRateController.php
│       │   ├── BankAccountController.php
│       │   ├── BankTransactionController.php
│       │   ├── ReconciliationController.php
│       │   └── ReportController.php
│       └── Requests/
│           ├── StoreChartOfAccountRequest.php
│           ├── UpdateChartOfAccountRequest.php
│           ├── StoreJournalEntryRequest.php
│           ├── UpdateJournalEntryRequest.php
│           ├── StoreFiscalYearRequest.php
│           ├── UpdateFiscalYearRequest.php
│           ├── StoreBudgetRequest.php
│           ├── UpdateBudgetRequest.php
│           ├── StoreTaxRateRequest.php
│           ├── UpdateTaxRateRequest.php
│           ├── StoreBankAccountRequest.php
│           ├── UpdateBankAccountRequest.php
│           ├── StoreBankTransactionRequest.php
│           ├── UpdateBankTransactionRequest.php
│           ├── StoreReconciliationRequest.php
│           └── UpdateReconciliationRequest.php
├── Routes/
│   └── api.php
├── database/
│   └── migrations/tenant/
│       ├── 2024_01_01_000001_rename_chart_of_accounts_to_acc_chart_of_accounts.php
│       ├── 2024_01_01_000002_rename_journal_entries_to_acc_journal_entries.php
│       ├── 2024_01_01_000003_rename_journal_items_to_acc_journal_items.php
│       ├── 2024_01_01_000004_rename_fiscal_years_to_acc_fiscal_years.php
│       ├── 2024_01_01_000005_create_acc_budgets_table.php
│       ├── 2024_01_01_000006_create_acc_budget_items_table.php
│       ├── 2024_01_01_000007_create_acc_tax_rates_table.php
│       ├── 2024_01_01_000008_create_acc_bank_accounts_table.php
│       ├── 2024_01_01_000009_create_acc_bank_transactions_table.php
│       ├── 2024_01_01_000010_create_acc_reconciliations_table.php
│       └── 2024_01_01_000011_add_fields_to_acc_chart_of_accounts.php
└── Providers/
    ├── AccountingServiceProvider.php
    └── EventServiceProvider.php
```

## Key Design Decisions

1. **Table Renames**: Original unprefixed tables (`chart_of_accounts`, `journal_entries`, etc.) renamed to `acc_` prefix for consistency
2. **Double-Entry Bookkeeping**: Journal entries must have balanced debit/credit totals (enforced by `JournalValidation` strategy)
3. **Fiscal Year Locking**: Journal entries can only be posted within an open fiscal year period
4. **Strategy Pattern**: Validation, calculation, and report generation are pluggable via strategy interfaces
5. **Repository Pattern**: All persistence goes through repository interfaces, bound in `AccountingServiceProvider`
6. **Form Requests**: All store/update endpoints use dedicated Form Request classes with `$request->validated()`
7. **TableListTrait**: Server-side pagination, search, and sorting via `App\Repositories\Traits\TableListTrait`

## API Response Format

All controllers use the `ApiResponseEnvelope` trait:

```json
// Success
{ "status": "success", "data": {...}, "message": "..." }

// Paginated
{ "status": "success", "data": [...], "message": "...", "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 75 } }

// Error
{ "status": "error", "data": null, "message": "...", "errors": {...} }
```
