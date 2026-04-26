# Expenses & Accounting Modules — Full DDD Build Plan

Build two new DDD modules (Expenses from scratch, Accounting restructured from old-style to DDD with table renames), fully integrated with each other, with full frontend dashboards + CRUD pages.

---

## Current State

- **Accounting**: Exists with old-style structure (`app/Models/`, `Http/Controllers/`). Has 4 migrations with **unprefixed** table names (`chart_of_accounts`, `journal_entries`, `journal_items`, `fiscal_years`). Only `ChartOfAccount` model is fleshed out; the other 3 are stubs. Routes use old `auth:sanctum`.
- **Expenses**: Does not exist. Seeder entry is a placeholder with `status: 'inactive'`.

## Module Design

### Accounting Module — Entities (10)

| # | Entity | Table | Notes |
|---|--------|-------|-------|
| 1 | ChartOfAccount | `acc_chart_of_accounts` | Rename from `chart_of_accounts`. Tree structure (parent_id). |
| 2 | JournalEntry | `acc_journal_entries` | Rename from `journal_entries`. Header with state machine. |
| 3 | JournalItem | `acc_journal_items` | Rename from `journal_items`. Line items (debit/credit). |
| 4 | FiscalYear | `acc_fiscal_years` | Rename from `fiscal_years`. Period management. |
| 5 | Budget | `acc_budgets` | NEW. Per fiscal year, per department. |
| 6 | BudgetItem | `acc_budget_items` | NEW. Line items per budget per account. |
| 7 | TaxRate | `acc_tax_rates` | NEW. Configurable tax rates. |
| 8 | BankAccount | `acc_bank_accounts` | NEW. Bank accounts for reconciliation. |
| 9 | BankTransaction | `acc_bank_transactions` | NEW. Imported/manual bank txns. |
| 10 | Reconciliation | `acc_reconciliations` | NEW. Match bank txns to journal entries. |

**Value Objects:** `AccountType`, `AccountSubType`, `JournalEntryState`, `FiscalYearStatus`, `BudgetStatus`, `BankTransactionType`, `ReconciliationStatus`

**Strategies:**
- `JournalValidation` — balanced debit/credit check, fiscal year period check
- `BalanceCalculation` — standard vs accrual-based account balance
- `ReportGeneration` — P&L, Balance Sheet, Trial Balance, Cash Flow

**Navigation (sidebar):**
| Section | Items |
|---------|-------|
| Main | Dashboard |
| Core | Chart of Accounts, Journal Entries, Fiscal Years |
| Planning | Budgets, Tax Rates |
| Banking | Bank Accounts, Reconciliation |
| Insights | Reports |

### Expenses Module — Entities (6)

| # | Entity | Table | Notes |
|---|--------|-------|-------|
| 1 | Expense | `exp_expenses` | Core entity. Amount, date, category, receipt, status. |
| 2 | ExpenseCategory | `exp_categories` | Hierarchical categories (parent_id). |
| 3 | ExpenseReport | `exp_reports` | Group expenses for approval workflow. |
| 4 | ExpensePolicy | `exp_policies` | Rules: max amounts, receipt requirements, auto-approval thresholds. |
| 5 | Receipt | `exp_receipts` | Uploaded receipt files (polymorphic to expense). |
| 6 | Reimbursement | `exp_reimbursements` | Track reimbursement payments to employees. |

**Value Objects:** `ExpenseStatus`, `ReportStatus`, `PolicyType`, `ReimbursementStatus`, `ExpenseCurrency`

**Strategies:**
- `ExpenseApproval` — auto-approve below threshold, manager approval, policy-based
- `ReimbursementProcessing` — manual vs auto
- `ReceiptProcessing` — manual upload (OCR stub for future)
- `PolicyValidation` — validate expense against policies

**Navigation (sidebar):**
| Section | Items |
|---------|-------|
| Main | Dashboard |
| Management | Expenses, Categories, Expense Reports |
| Finance | Reimbursements |
| Settings | Policies |

### Cross-Module Integration (Expenses → Accounting)

- **Event:** `ExpenseReportApproved` → **Listener:** `CreateJournalEntryFromExpenseReport`
- Creates a journal entry: debit expense account (mapped via category), credit accounts payable/reimbursement account
- Integration lives in `Expenses/Infrastructure/Integrations/AccountingIntegration.php`

---

## Implementation Epics (ordered)

### Epic 1: Accounting — Table Renames + DDD Restructure ✅
1. ✅ Create rename migrations: `chart_of_accounts` → `acc_chart_of_accounts`, etc.
2. ✅ Delete old-style directories (`app/`, `Http/`, `Repositories/`, `Services/`)
3. ✅ Create DDD directory structure (`Domain/`, `Application/`, `Infrastructure/`, `Presentation/`)
4. ✅ Create Value Objects (7 enums)
5. ✅ Create Domain Exceptions
6. ✅ Create Strategy interfaces + default implementations (3)
7. ✅ Restructure `ChartOfAccount` as rich domain entity with `$table = 'acc_chart_of_accounts'`
8. ✅ Flesh out `JournalEntry`, `JournalItem`, `FiscalYear` as rich entities
9. ✅ Create Repository interfaces + Eloquent implementations (4)
10. ✅ Create DTOs (Create/Update per entity)
11. ✅ Create UseCases (CRUD + PostJournalEntry, CancelJournalEntry, CloseFiscalYear)
12. ✅ Create Domain Events + Listeners
13. ✅ Update ServiceProvider with DDD bindings
14. ✅ Update `module.json`

### Epic 2: Accounting — New Entities (Budget, TaxRate, BankAccount, BankTransaction, Reconciliation) ✅
1. ✅ Create migrations for 6 new tables
2. ✅ Create Value Objects for new entities
3. ✅ Create rich Domain Entities
4. ✅ Create Repository interfaces + implementations
5. ✅ Create DTOs + UseCases
6. ✅ Create Domain Events

### Epic 3: Accounting — API Layer ✅
1. ✅ Create Form Requests (Store/Update per entity)
2. ✅ Create API Controllers (10 controllers)
3. ✅ Create Routes (`/tenant/accounting/...`)
4. ✅ Create Dashboard API controller (stats, recent entries, balance summary)
5. ✅ Create Reports API controller (P&L, Balance Sheet, Trial Balance)

### Epic 4: Expenses — Full Backend (from scratch) ✅
1. ✅ Create module skeleton (`module.json`, ServiceProvider, EventServiceProvider)
2. ✅ Create migrations (8 tables + pivots)
3. ✅ Create Value Objects (5 enums)
4. ✅ Create Domain Exceptions
5. ✅ Create Strategy interfaces + default implementations (4)
6. ✅ Create rich Domain Entities (6)
7. ✅ Create Repository interfaces + implementations (6)
8. ✅ Create DTOs + UseCases (CRUD + SubmitExpense, ApproveExpense, RejectExpense, SubmitReport)
9. ✅ Create Domain Events + Listeners
10. ✅ Create Form Requests, API Controllers, Routes
11. ✅ Create Dashboard API controller

### Epic 5: Cross-Module Integration ✅
1. ✅ Create `CreateJournalEntryOnExpenseApproved` listener in Expenses module
2. ✅ Create `CreateJournalEntryOnReimbursement` listener in Expenses module
3. ✅ Create `ExpenseAccountMapping` — using `category.default_account_id` field (no separate table needed)
4. ✅ Wire events in EventServiceProvider

### Epic 6: Frontend — Accounting ✅
1. ✅ Add API client functions (`accounting-resources.ts`)
2. ✅ Create module layout (`layout.tsx`) + navigation (via ModulesSeeder)
3. ✅ Create Dashboard page (stats cards)
4. ✅ Create CRUD pages: Chart of Accounts, Journal Entries, Fiscal Years, Budgets, Tax Rates, Bank Accounts (SimpleCRUDPage with serverSide pagination)
5. ✅ Create Reports page (P&L, Balance Sheet, Trial Balance, Cash Flow)

### Epic 7: Frontend — Expenses ✅
1. ✅ Add API client functions (`expenses-resources.ts`)
2. ✅ Create module layout (`layout.tsx`) + navigation (via ModulesSeeder)
3. ✅ Create Dashboard page (stats cards)
4. ✅ Create CRUD pages: Expenses, Categories, Expense Reports, Policies, Tags, Reimbursements (SimpleCRUDPage with serverSide pagination)
5. ✅ Create Expense Report detail view (grouped expenses, approval actions)

### Epic 8: ModulesSeeder + RBAC + Postman ✅
1. ✅ Update ModulesSeeder: add navigation arrays, update descriptions/routes for both modules, set status to 'active'
2. ✅ Create permission seeders for both modules (`AccountingPermissionSeeder`, `ExpensesPermissionSeeder`)
3. ✅ Update Postman collection with Accounting + Expenses folders
4. ✅ Create AGENTS.md for both modules

---

## File Counts (Estimate)

| Layer | Accounting | Expenses | Total |
|-------|-----------|----------|-------|
| Migrations | 4 rename + 6 new | 6 | 16 |
| Value Objects | 7 | 5 | 12 |
| Entities | 10 | 6 | 16 |
| Repositories (interface + impl) | 10 × 2 | 6 × 2 | 32 |
| DTOs | ~14 | ~10 | 24 |
| UseCases | ~18 | ~14 | 32 |
| Events + Listeners | ~8 + ~4 | ~6 + ~4 | 22 |
| Strategies | 3 × 2 | 4 × 2 | 14 |
| Controllers | 10 | 7 | 17 |
| Form Requests | ~14 | ~10 | 24 |
| Frontend pages | ~10 | ~7 | 17 |
| **Total files** | | | **~220+** |

## Suggested Build Order

Start with **Epic 1** (restructure Accounting core), then **Epic 4** (Expenses backend), then APIs (Epics 2-3), then integration (Epic 5), then frontend (Epics 6-7), then seeder/Postman (Epic 8).

This is a ~220+ file task. I recommend building one epic at a time with verification between each.
