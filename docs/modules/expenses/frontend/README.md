# Expenses Module — Frontend

## Page Components

All CRUD pages use the `SimpleCRUDPage` component with server-side pagination, except the Report detail view which is a custom component.

### Dashboard (`page.tsx`)
Stats cards showing:
- Total expenses count and amount
- Pending expenses count
- Approved expenses count
- Total reimbursements amount

### CRUD Pages (SimpleCRUDPage)

| Page | Route | Entity | Special Fields |
|------|-------|--------|----------------|
| Categories | `/dashboard/modules/expenses/categories` | ExpenseCategory | parent_id (hierarchical), default_account_id, requires_receipt, max_amount |
| Expenses | `/dashboard/modules/expenses/expenses` | Expense | amount, currency, date, category_id (select), status, is_billable |
| Reports | `/dashboard/modules/expenses/reports` | ExpenseReport | title, description, status, total_amount |
| Policies | `/dashboard/modules/expenses/policies` | ExpensePolicy | type (select: max_amount/receipt_required/approval_required/category_restriction), rules (array), priority |
| Tags | `/dashboard/modules/expenses/tags` | ExpenseTag | name, color |
| Reimbursements | `/dashboard/modules/expenses/reimbursements` | Reimbursement | reference, amount, currency, payment_method, status |

### Report Detail View (`reports/[id]/page.tsx`)
Custom page (not SimpleCRUDPage) with:
- Report header (title, description, status badge)
- Grouped expense list
- Approval actions (submit, approve, reject) with status-dependent visibility
- Uses `AbortController` for safe data fetching

## API Client

All API functions are in `@/lib/expenses-resources.ts`:

```typescript
// CRUD pattern per entity (example for Expenses)
listExpenses<T>(params?: TableParams): Promise<PaginatedResponse<T>>
createExpense(payload: Record<string, unknown>)
updateExpense(id: number, payload: Record<string, unknown>)
deleteExpense(id: number)

// Dashboard
getExpensesDashboard()

// Expense workflow actions
submitExpense(id: number)
approveExpense(id: number)
rejectExpense(id: number, reason: string)

// Report workflow actions
submitReport(id: number)
approveReport(id: number)
rejectReport(id: number, reason: string)

// Reimbursement workflow action
processReimbursement(id: number)
```

## Navigation (from ModulesSeeder)

```json
{
  "sections": [
    { "key": "main", "label": "Main", "items": [
      { "key": "dashboard", "label": "Dashboard", "route": "/dashboard/modules/expenses", "icon": "LayoutDashboard" }
    ]},
    { "key": "management", "label": "Management", "items": [
      { "key": "expenses", "label": "Expenses", "route": "/dashboard/modules/expenses/expenses", "icon": "Receipt" },
      { "key": "categories", "label": "Categories", "route": "/dashboard/modules/expenses/categories", "icon": "FolderTree" },
      { "key": "reports", "label": "Expense Reports", "route": "/dashboard/modules/expenses/reports", "icon": "FileText" }
    ]},
    { "key": "finance", "label": "Finance", "items": [
      { "key": "reimbursements", "label": "Reimbursements", "route": "/dashboard/modules/expenses/reimbursements", "icon": "Banknote" }
    ]},
    { "key": "settings", "label": "Settings", "items": [
      { "key": "policies", "label": "Policies", "route": "/dashboard/modules/expenses/policies", "icon": "ShieldCheck" },
      { "key": "tags", "label": "Tags", "route": "/dashboard/modules/expenses/tags", "icon": "Tag" }
    ]}
  ]
}
```

## Status Badges

The Report detail view uses color-coded status badges:

| Status | Color | Description |
|--------|-------|-------------|
| `draft` | Gray | Initial state, editable |
| `pending` / `submitted` | Yellow | Awaiting approval |
| `approved` | Green | Approved by manager |
| `rejected` | Red | Rejected with reason |
| `reimbursed` / `completed` | Blue | Payment processed |
| `processing` | Purple | Reimbursement in progress |
| `failed` | Red | Reimbursement failed |
