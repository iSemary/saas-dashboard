# Accounting Module

## Overview

The Accounting module provides comprehensive financial management capabilities including:

- **Chart of Accounts**: Hierarchical account structure (asset, liability, equity, income, expense)
- **Journal Entries**: Double-entry bookkeeping with balanced debit/credit validation
- **Fiscal Years**: Period management with open/closed/locked states
- **Budgets**: Budget tracking with planned vs actual amounts per fiscal year
- **Tax Rates**: Configurable tax rates with compound tax support
- **Bank Accounts**: Bank account management with balance tracking
- **Bank Transactions**: Imported/manual bank transaction records
- **Reconciliation**: Bank statement matching and reconciliation
- **Reports**: Trial Balance, Profit & Loss, Balance Sheet, Cash Flow

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Listeners
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/Accounting/
├── Domain/
│   ├── Entities/           - ChartOfAccount, JournalEntry, JournalItem, FiscalYear,
│   │                         Budget, BudgetItem, TaxRate, BankAccount,
│   │                         BankTransaction, Reconciliation
│   ├── ValueObjects/       - AccountType, AccountSubType, JournalEntryState,
│   │                         FiscalYearStatus, BudgetStatus, BankTransactionType,
│   │                         ReconciliationStatus
│   ├── Events/             - JournalEntryCreated, JournalEntryPosted,
│   │                         FiscalYearCreated, BudgetCreated
│   ├── Exceptions/         - UnbalancedJournalEntry, InvalidJournalEntryTransition,
│   │                         InvalidFiscalYearTransition, FiscalYearClosed
│   └── Strategies/
│       ├── JournalValidation/        - Balanced debit/credit check, fiscal year period check
│       ├── BalanceCalculation/       - Standard vs accrual-based account balance
│       └── ReportGeneration/         - P&L, Balance Sheet, Trial Balance, Cash Flow
├── Application/
│   ├── DTOs/               - Create/Update DTOs per entity (20 DTOs)
│   └── UseCases/           - CRUD + PostJournalEntry, CancelJournalEntry,
│                             CloseFiscalYear, GenerateReport
├── Infrastructure/
│   ├── Persistence/        - 10 repository interfaces + Eloquent implementations
│   └── Listeners/          - RecalculateAccountBalances (on JournalEntryPosted)
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/ - 10 controllers (8 CRUD + Dashboard + Report)
│       └── Requests/       - Store/Update form requests per entity (16 requests)
├── Routes/
│   └── api.php             - All API routes under /tenant/accounting/
├── database/
│   └── migrations/tenant/  - 4 rename + 7 new entity migrations
└── Providers/
    ├── AccountingServiceProvider.php    - Repository + strategy bindings
    └── EventServiceProvider.php         - Event listener registrations
```

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/accounting/
├── page.tsx                  - Accounting Dashboard (stats cards)
├── layout.tsx                - Module layout wrapper
├── chart-of-accounts/        - Chart of Accounts CRUD (SimpleCRUDPage)
├── journal-entries/          - Journal Entries CRUD (SimpleCRUDPage)
├── fiscal-years/             - Fiscal Years CRUD (SimpleCRUDPage)
├── budgets/                  - Budgets CRUD (SimpleCRUDPage)
├── tax-rates/                - Tax Rates CRUD (SimpleCRUDPage)
├── bank-accounts/            - Bank Accounts CRUD (SimpleCRUDPage)
├── bank-transactions/        - Bank Transactions CRUD (SimpleCRUDPage)
├── reconciliation/           - Reconciliation CRUD (SimpleCRUDPage)
└── reports/                  - Reports page (Trial Balance, P&L, Balance Sheet, Cash Flow)
```

## API Routes

All routes are prefixed with `/tenant/accounting` and require `auth:api` + `tenant_roles` + `throttle:60,1` middleware.

### Dashboard
- `GET /tenant/accounting/dashboard/stats` - Dashboard statistics
- `GET /tenant/accounting/dashboard/recent-entries` - Recent journal entries
- `GET /tenant/accounting/dashboard/account-balances` - Account balances grouped by type

### Reports
- `GET /tenant/accounting/reports/{type}` - Generate report (trial_balance, profit_loss, balance_sheet, cash_flow)

### Chart of Accounts
- `GET /tenant/accounting/chart-of-accounts` - List accounts
- `POST /tenant/accounting/chart-of-accounts` - Create account
- `GET /tenant/accounting/chart-of-accounts/{id}` - Get account
- `PUT /tenant/accounting/chart-of-accounts/{id}` - Update account
- `DELETE /tenant/accounting/chart-of-accounts/{id}` - Delete account
- `POST /tenant/accounting/chart-of-accounts/bulk-destroy` - Bulk delete

### Journal Entries
- `GET /tenant/accounting/journal-entries` - List entries
- `POST /tenant/accounting/journal-entries` - Create entry
- `GET /tenant/accounting/journal-entries/{id}` - Get entry
- `PUT /tenant/accounting/journal-entries/{id}` - Update entry
- `DELETE /tenant/accounting/journal-entries/{id}` - Delete entry
- `POST /tenant/accounting/journal-entries/bulk-destroy` - Bulk delete
- `POST /tenant/accounting/journal-entries/{id}/post` - Post entry (draft → posted)
- `POST /tenant/accounting/journal-entries/{id}/cancel` - Cancel entry (draft → cancelled)

### Fiscal Years
- `GET /tenant/accounting/fiscal-years` - List fiscal years
- `POST /tenant/accounting/fiscal-years` - Create fiscal year
- `GET /tenant/accounting/fiscal-years/{id}` - Get fiscal year
- `PUT /tenant/accounting/fiscal-years/{id}` - Update fiscal year
- `DELETE /tenant/accounting/fiscal-years/{id}` - Delete fiscal year
- `POST /tenant/accounting/fiscal-years/{id}/close` - Close fiscal year

### Budgets
- `GET /tenant/accounting/budgets` - List budgets
- `POST /tenant/accounting/budgets` - Create budget
- `GET /tenant/accounting/budgets/{id}` - Get budget
- `PUT /tenant/accounting/budgets/{id}` - Update budget
- `DELETE /tenant/accounting/budgets/{id}` - Delete budget
- `POST /tenant/accounting/budgets/bulk-destroy` - Bulk delete

### Tax Rates
- `GET /tenant/accounting/tax-rates` - List tax rates
- `POST /tenant/accounting/tax-rates` - Create tax rate
- `GET /tenant/accounting/tax-rates/{id}` - Get tax rate
- `PUT /tenant/accounting/tax-rates/{id}` - Update tax rate
- `DELETE /tenant/accounting/tax-rates/{id}` - Delete tax rate
- `POST /tenant/accounting/tax-rates/bulk-destroy` - Bulk delete

### Bank Accounts
- `GET /tenant/accounting/bank-accounts` - List bank accounts
- `POST /tenant/accounting/bank-accounts` - Create bank account
- `GET /tenant/accounting/bank-accounts/{id}` - Get bank account
- `PUT /tenant/accounting/bank-accounts/{id}` - Update bank account
- `DELETE /tenant/accounting/bank-accounts/{id}` - Delete bank account
- `POST /tenant/accounting/bank-accounts/bulk-destroy` - Bulk delete

### Bank Transactions
- `GET /tenant/accounting/bank-transactions` - List transactions
- `POST /tenant/accounting/bank-transactions` - Create transaction
- `GET /tenant/accounting/bank-transactions/{id}` - Get transaction
- `PUT /tenant/accounting/bank-transactions/{id}` - Update transaction
- `DELETE /tenant/accounting/bank-transactions/{id}` - Delete transaction
- `POST /tenant/accounting/bank-transactions/bulk-destroy` - Bulk delete

### Reconciliation
- `GET /tenant/accounting/reconciliations` - List reconciliations
- `POST /tenant/accounting/reconciliations` - Create reconciliation
- `GET /tenant/accounting/reconciliations/{id}` - Get reconciliation
- `PUT /tenant/accounting/reconciliations/{id}` - Update reconciliation
- `DELETE /tenant/accounting/reconciliations/{id}` - Delete reconciliation
- `POST /tenant/accounting/reconciliations/{id}/complete` - Complete reconciliation

## Database Tables

All tables use the `acc_` prefix:

| Table | Description |
|-------|-------------|
| `acc_chart_of_accounts` | Hierarchical chart of accounts with parent-child structure |
| `acc_journal_entries` | Journal entry headers with state machine (draft/posted/cancelled) |
| `acc_journal_items` | Journal entry line items (debit/credit) |
| `acc_fiscal_years` | Fiscal year periods (open/closed/locked) |
| `acc_budgets` | Budgets per fiscal year per department |
| `acc_budget_items` | Budget line items per account |
| `acc_tax_rates` | Configurable tax rates |
| `acc_bank_accounts` | Bank account details |
| `acc_bank_transactions` | Bank transaction records |
| `acc_reconciliations` | Bank reconciliation records |

## Permissions

The Accounting module includes ~45 permissions grouped by entity:

- `accounting.dashboard.view`
- `accounting.chart_of_accounts.view/create/edit/delete`
- `accounting.journal_entries.view/create/edit/delete/post/cancel`
- `accounting.fiscal_years.view/create/edit/delete/close`
- `accounting.budgets.view/create/edit/delete`
- `accounting.tax_rates.view/create/edit/delete`
- `accounting.bank_accounts.view/create/edit/delete`
- `accounting.bank_transactions.view/create/edit/delete`
- `accounting.reconciliation.view/create/edit/delete/complete`
- `accounting.reports.view/generate`

### Role Assignments

| Permission Set | Admin | Accountant | Manager | Viewer |
|---------------|-------|-----------|---------|--------|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Chart of Accounts (full) | ✅ | ✅ | view | view |
| Journal Entries (full + post/cancel) | ✅ | ✅ | view/create | view |
| Fiscal Years (full + close) | ✅ | ✅ | view | view |
| Budgets (full) | ✅ | ✅ | view/create/edit | view |
| Tax Rates (full) | ✅ | ✅ | view | view |
| Bank Accounts (full) | ✅ | ✅ | view | view |
| Bank Transactions (full) | ✅ | ✅ | view/create | view |
| Reconciliation (full + complete) | ✅ | ✅ | view | view |
| Reports (view + generate) | ✅ | ✅ | ✅ | ✅ |

## Strategy Pattern

### JournalValidation
Validates that journal entries have balanced debit/credit totals and fall within an open fiscal year period.
- `DefaultJournalValidationStrategy` - Standard balanced entry validation

### BalanceCalculation
Calculates account balances from journal items plus opening balance.
- `DefaultBalanceCalculationStrategy` - Standard balance calculation

### ReportGeneration
Generates financial reports from account and journal entry data.
- `DefaultReportGenerationStrategy` - Generates trial balance, P&L, balance sheet, cash flow

## Domain Events

- `JournalEntryCreated` - Fired when a new journal entry is created
- `JournalEntryPosted` - Fired when a journal entry is posted (triggers balance recalculation)
- `FiscalYearCreated` - Fired when a new fiscal year is created
- `BudgetCreated` - Fired when a new budget is created

## Entity State Machines

- **JournalEntry**: `draft` → `posted` → `cancelled`
- **FiscalYear**: `open` → `closed` → `locked`
- **Budget**: `draft` → `active` → `archived`
- **Reconciliation**: `draft` → `in_progress` → `completed`

## Cross-Module Integration

The Accounting module receives events from the **Expenses** module:

- `ExpenseApproved` → `CreateJournalEntryOnExpenseApproved` → Creates journal entry (debit expense account, credit cash/AP)
- `ExpenseReimbursed` → `CreateJournalEntryOnReimbursement` → Creates journal entry (debit AP, credit cash)

The account mapping is determined by `ExpenseCategory.default_account_id`.

## Installation

1. Run migrations:
```bash
php artisan migrate --path=modules/Accounting/database/migrations/tenant
```

2. Seed permissions:
```bash
php artisan db:seed --class=AccountingPermissionSeeder
```

3. Clear module cache:
```bash
php artisan config:clear
```

## Development Notes

- All entities use the `acc_` table prefix
- All entities have `custom_fields` JSON column for extensibility
- Tenant-scoped via separate database per tenant (no `tenant_id` columns)
- Rich entities with business methods (e.g., `JournalEntry::transitionState()`, `FiscalYear::close()`)
- Repository pattern for persistence abstraction
- UseCase pattern for business logic
- Form Request validation on all store/update endpoints
- `ApiResponseEnvelope` trait for consistent API responses
- `TableListTrait` for server-side pagination, search, and sorting
