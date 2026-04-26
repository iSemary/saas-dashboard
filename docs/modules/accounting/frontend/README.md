# Accounting Module — Frontend

## Page Components

All CRUD pages use the `SimpleCRUDPage` component with server-side pagination.

### Dashboard (`page.tsx`)
Stats cards showing:
- Active accounts count
- Draft/posted journal entries count
- Active fiscal years count
- Active budgets count
- Total debit/credit amounts

### CRUD Pages (SimpleCRUDPage)

| Page | Route | Entity | Special Fields |
|------|-------|--------|----------------|
| Chart of Accounts | `/dashboard/modules/accounting/chart-of-accounts` | ChartOfAccount | type (select: asset/liability/equity/income/expense), parent_id (hierarchical) |
| Journal Entries | `/dashboard/modules/accounting/journal-entries` | JournalEntry | state (draft/posted/cancelled), fiscal_year_id |
| Fiscal Years | `/dashboard/modules/accounting/fiscal-years` | FiscalYear | start_date, end_date, is_active |
| Budgets | `/dashboard/modules/accounting/budgets` | Budget | fiscal_year_id, department_id, status |
| Tax Rates | `/dashboard/modules/accounting/tax-rates` | TaxRate | rate (numeric), type, is_compound |
| Bank Accounts | `/dashboard/modules/accounting/bank-accounts` | BankAccount | bank_name, account_number, currency |
| Bank Transactions | `/dashboard/modules/accounting/bank-transactions` | BankTransaction | type (deposit/withdrawal/transfer), amount, bank_account_id |
| Reconciliation | `/dashboard/modules/accounting/reconciliation` | Reconciliation | statement_date, statement_balance, book_balance, status |

### Reports Page (`reports/page.tsx`)
Custom page (not SimpleCRUDPage) with:
- Report type selector (Trial Balance, P&L, Balance Sheet, Cash Flow)
- Date range picker
- Report data display

## API Client

All API functions are in `@/lib/accounting-resources.ts`:

```typescript
// CRUD pattern per entity (example for Chart of Accounts)
listChartOfAccounts<T>(params?: TableParams): Promise<PaginatedResponse<T>>
createChartOfAccount(payload: Record<string, unknown>)
updateChartOfAccount(id: number, payload: Record<string, unknown>)
deleteChartOfAccount(id: number)

// Dashboard
getAccountingDashboard()

// Reconciliation extra action
completeReconciliation(id: number)
```

## Navigation (from ModulesSeeder)

```json
{
  "sections": [
    { "key": "main", "label": "Main", "items": [
      { "key": "dashboard", "label": "Dashboard", "route": "/dashboard/modules/accounting", "icon": "LayoutDashboard" }
    ]},
    { "key": "core", "label": "Core", "items": [
      { "key": "chart-of-accounts", "label": "Chart of Accounts", "route": "/dashboard/modules/accounting/chart-of-accounts", "icon": "ListTree" },
      { "key": "journal-entries", "label": "Journal Entries", "route": "/dashboard/modules/accounting/journal-entries", "icon": "BookOpen" },
      { "key": "fiscal-years", "label": "Fiscal Years", "route": "/dashboard/modules/accounting/fiscal-years", "icon": "Calendar" }
    ]},
    { "key": "planning", "label": "Planning", "items": [
      { "key": "budgets", "label": "Budgets", "route": "/dashboard/modules/accounting/budgets", "icon": "PiggyBank" },
      { "key": "tax-rates", "label": "Tax Rates", "route": "/dashboard/modules/accounting/tax-rates", "icon": "Percent" }
    ]},
    { "key": "banking", "label": "Banking", "items": [
      { "key": "bank-accounts", "label": "Bank Accounts", "route": "/dashboard/modules/accounting/bank-accounts", "icon": "Building2" },
      { "key": "bank-transactions", "label": "Bank Transactions", "route": "/dashboard/modules/accounting/bank-transactions", "icon": "ArrowLeftRight" },
      { "key": "reconciliation", "label": "Reconciliation", "route": "/dashboard/modules/accounting/reconciliation", "icon": "CheckCircle" }
    ]},
    { "key": "insights", "label": "Insights", "items": [
      { "key": "reports", "label": "Reports", "route": "/dashboard/modules/accounting/reports", "icon": "BarChart3" }
    ]}
  ]
}
```
