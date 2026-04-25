# Accounting Module Documentation

## Overview

The Accounting module provides comprehensive financial accounting functionality including chart of accounts, journal entries, transactions, financial reporting, and account reconciliation. It enables tracking of financial transactions, generating financial statements, and managing the organization's financial health.

## Architecture

### Module Structure

```
Accounting/
├── Config/              # Module configuration
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations and seeders
```

## Database Schema

### Core Entities

#### Chart of Accounts
- `id` - Primary key
- `account_code` - Account code
- `account_name` - Account name
- `account_type` - Account type (asset, liability, equity, revenue, expense)
- `parent_id` - Parent account (for hierarchy)
- `description` - Account description
- `balance` - Current balance
- `status` - Account status
- `created_at`, `updated_at` - Timestamps

#### Journal Entries
- `id` - Primary key
- `entry_number` - Entry number
- `entry_date` - Entry date
- `description` - Entry description
- `reference` - Reference number
- `status` - Entry status (draft, posted, reversed)
- `total_debit` - Total debit amount
- `total_credit` - Total credit amount
- `created_by` - User who created entry
- `created_at`, `updated_at` - Timestamps

#### Journal Entry Lines
- `id` - Primary key
- `journal_entry_id` - Associated journal entry
- `account_id` - Associated account
- `debit_amount` - Debit amount
- `credit_amount` - Credit amount
- `description` - Line description
- `created_at`, `updated_at` - Timestamps

#### Transactions
- `id` - Primary key
- `transaction_number` - Transaction number
- `transaction_date` - Transaction date
- `transaction_type` - Transaction type
- `amount` - Transaction amount
- `account_id` - Associated account
- `description` - Transaction description
- `reference` - Reference number
- `status` - Transaction status
- `created_at`, `updated_at` - Timestamps

#### Financial Periods
- `id` - Primary key
- `period_name` - Period name (e.g., "Q1 2024")
- `start_date` - Period start date
- `end_date` - Period end date
- `status` - Period status (open, closed)
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Chart of Accounts

**List Accounts:** `GET /api/tenant/accounting/accounts`
**Create Account:** `POST /api/tenant/accounting/accounts`
**Get Account:** `GET /api/tenant/accounting/accounts/{id}`
**Update Account:** `PUT /api/tenant/accounting/accounts/{id}`
**Delete Account:** `DELETE /api/tenant/accounting/accounts/{id}`
**Get Account Balance:** `GET /api/tenant/accounting/accounts/{id}/balance`

### Journal Entries

**List Journal Entries:** `GET /api/tenant/accounting/journal-entries`
**Create Journal Entry:** `POST /api/tenant/accounting/journal-entries`
**Get Journal Entry:** `GET /api/tenant/accounting/journal-entries/{id}`
**Update Journal Entry:** `PUT /api/tenant/accounting/journal-entries/{id}`
**Delete Journal Entry:** `DELETE /api/tenant/accounting/journal-entries/{id}`
**Post Journal Entry:** `POST /api/tenant/accounting/journal-entries/{id}/post`
**Reverse Journal Entry:** `POST /api/tenant/accounting/journal-entries/{id}/reverse`

### Transactions

**List Transactions:** `GET /api/tenant/accounting/transactions`
**Get Transaction:** `GET /api/tenant/accounting/transactions/{id}`
**Create Transaction:** `POST /api/tenant/accounting/transactions`

### Financial Periods

**List Periods:** `GET /api/tenant/accounting/periods`
**Create Period:** `POST /api/tenant/accounting/periods`
**Get Period:** `GET /api/tenant/accounting/periods/{id}`
**Update Period:** `PUT /api/tenant/accounting/periods/{id}`
**Close Period:** `POST /api/tenant/accounting/periods/{id}/close`
**Reopen Period:** `POST /api/tenant/accounting/periods/{id}/reopen`

### Reports

**Balance Sheet:** `GET /api/tenant/accounting/reports/balance-sheet`
**Income Statement:** `GET /api/tenant/accounting/reports/income-statement`
**Trial Balance:** `GET /api/tenant/accounting/reports/trial-balance`
**Cash Flow:** `GET /api/tenant/accounting/reports/cash-flow`

## Services

### AccountService
- Account CRUD operations
- Account hierarchy management
- Balance calculations
- Account type validation

### JournalEntryService
- Journal entry CRUD operations
- Entry posting logic
- Entry reversal
- Debit/credit validation

### TransactionService
- Transaction recording
- Transaction-account associations
- Transaction categorization

### FinancialReportService
- Financial statement generation
- Balance sheet calculation
- Income statement calculation
- Trial balance generation

## Repositories

### AccountRepository
- Account data access
- Account hierarchy queries
- Account type filtering

### JournalEntryRepository
- Journal entry data access
- Entry filtering and searching
- Entry-line relationships

### TransactionRepository
- Transaction data access
- Transaction filtering and searching
- Period-based queries

### FinancialPeriodRepository
- Period data access
- Period status queries
- Date range filtering

## Account Types

### Asset Accounts
- Current assets (cash, accounts receivable, inventory)
- Fixed assets (property, equipment)
- Intangible assets

### Liability Accounts
- Current liabilities (accounts payable, short-term debt)
- Long-term liabilities (long-term debt, mortgages)

### Equity Accounts
- Owner's equity
- Retained earnings
- Common stock

### Revenue Accounts
- Sales revenue
- Service revenue
- Other income

### Expense Accounts
- Operating expenses
- Cost of goods sold
- Depreciation

## Configuration

### Module Configuration

Module configuration in `Config/accounting.php`:

```php
return [
    'accounts' => [
        'default_account_type' => 'asset',
        'allow_hierarchy' => true,
        'auto_balance_update' => true,
    ],
    'journal_entries' => [
        'auto_post' => false,
        'require_balanced' => true,
    ],
    'periods' => [
        'auto_close' => false,
        'close_days_after_end' => 30,
    ],
    'reports' => [
        'default_currency' => 'USD',
        'include_zero_balances' => false,
    ],
];
```

## Financial Reports

### Balance Sheet
- Assets
- Liabilities
- Equity
- Balance sheet equation validation

### Income Statement
- Revenue
- Expenses
- Net income/loss
- Gross margin calculations

### Trial Balance
- List of all accounts
- Debit balances
- Credit balances
- Balance validation

### Cash Flow Statement
- Operating activities
- Investing activities
- Financing activities
- Net cash flow

## Business Rules

- Journal entries must balance (debits = credits)
- Posted journal entries cannot be modified
- Closed financial periods cannot be modified
- Account codes must be unique
- Parent accounts must exist before child accounts
- Transactions must reference valid accounts

## Permissions

Accounting module permissions follow the pattern: `accounting.{resource}.{action}`

- `accounting.accounts.view` - View accounts
- `accounting.accounts.create` - Create accounts
- `accounting.accounts.edit` - Edit accounts
- `accounting.accounts.delete` - Delete accounts
- `accounting.journal_entries.view` - View journal entries
- `accounting.journal_entries.create` - Create journal entries
- `accounting.journal_entries.edit` - Edit journal entries
- `accounting.journal_entries.delete` - Delete journal entries
- `accounting.journal_entries.post` - Post journal entries
- `accounting.transactions.view` - View transactions
- `accounting.transactions.create` - Create transactions
- `accounting.periods.view` - View periods
- `accounting.periods.create` - Create periods
- `accounting.periods.close` - Close periods
- `accounting.reports.view` - View reports

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Accounting/Tests --testdox
```

Test coverage includes:
- Unit tests for account calculations
- Feature tests for API endpoints
- Integration tests for journal entry posting
- Report generation tests

## Related Documentation

- [Financial Reporting](../../backend/documentation/accounting/reports.md)
- [Accounting Best Practices](../../backend/documentation/accounting/best-practices.md)
