# Accounting Module — Developer Guide

## Overview
Tenant-level Accounting & Finance module using DDD + Strategy Pattern. Manages chart of accounts, journal entries, fiscal years, budgets, tax rates, bank accounts, bank transactions, and reconciliations.

## Architecture

```
Domain/          Pure business logic
  Entities/      ChartOfAccount, JournalEntry, JournalItem, FiscalYear, Budget, BudgetItem, TaxRate, BankAccount, BankTransaction, Reconciliation
  ValueObjects/  AccountType, AccountSubType, JournalEntryState, FiscalYearStatus, BudgetStatus, BankTransactionType, ReconciliationStatus
  Events/        JournalEntryCreated, JournalEntryPosted, FiscalYearCreated, BudgetCreated
  Strategies/    JournalValidation (DefaultJournalValidationStrategy), BalanceCalculation (DefaultBalanceCalculationStrategy), ReportGeneration (DefaultReportGenerationStrategy)
  Exceptions/    UnbalancedJournalEntry, InvalidJournalEntryTransition, InvalidFiscalYearTransition, FiscalYearClosed

Application/
  DTOs/          Create/Update DTOs per entity (10 entities × 2 = 20 DTOs)
  UseCases/      ChartOfAccountUseCase, JournalEntryUseCase, FiscalYearUseCase, BudgetUseCase, TaxRateUseCase, BankAccountUseCase, BankTransactionUseCase, ReconciliationUseCase, GenerateReportUseCase

Infrastructure/
  Persistence/   Repository interfaces + Eloquent implementations (10 entities)
  Listeners/     RecalculateAccountBalances (on JournalEntryPosted)

Presentation/    (future: Controllers, Requests, Routes)
```

## Route Prefix
`/tenant/accounting/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `acc_` prefix: `acc_chart_of_accounts`, `acc_journal_entries`, `acc_journal_items`, `acc_fiscal_years`, `acc_budgets`, `acc_budget_items`, `acc_tax_rates`, `acc_bank_accounts`, `acc_bank_transactions`, `acc_reconciliations`

## Strategy Pattern
- **JournalValidation**: Default validates balanced debit/credit entries
- **BalanceCalculation**: Default calculates account balance from journal items + opening balance
- **ReportGeneration**: Default generates trial balance, P&L, balance sheet, cash flow reports

## Key Features
- Double-entry bookkeeping with balanced journal entries
- Hierarchical chart of accounts (parent-child)
- Fiscal year management with open/closed/locked states
- Budget tracking with planned vs actual amounts
- Tax rate management with compound tax support
- Bank account management with balance tracking
- Bank reconciliation with statement matching
- Financial reporting (trial balance, P&L, balance sheet, cash flow)
- Domain events trigger automatic account balance recalculation

## Entity State Machines
- **JournalEntry**: draft → posted → cancelled
- **FiscalYear**: open → closed → locked
- **Budget**: draft → active → archived
- **Reconciliation**: draft → in_progress → completed
