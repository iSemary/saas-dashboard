# Expenses Module

## Overview

The Expenses module provides comprehensive expense management capabilities including:

- **Expense Categories**: Hierarchical categories with default account mapping and receipt policies
- **Expenses**: Core expense records with approval workflows and receipt tracking
- **Expense Reports**: Group expenses for batch approval and reimbursement
- **Expense Policies**: Rule-based validation (max amounts, receipt requirements, auto-approval thresholds)
- **Expense Tags**: Tagging system for expense categorization
- **Reimbursements**: Track reimbursement payments to employees

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Listeners (cross-module)
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/Expenses/
├── Domain/
│   ├── Entities/           - ExpenseCategory, Expense, ExpenseReport,
│   │                         ExpensePolicy, ExpenseTag, Reimbursement
│   ├── ValueObjects/       - ExpenseStatus, ReportStatus, PolicyType,
│   │                         ReimbursementStatus, ExpenseCurrency
│   ├── Events/             - ExpenseCreated, ExpenseSubmitted, ExpenseApproved,
│   │                         ExpenseRejected, ExpenseReimbursed,
│   │                         ReportSubmitted, ReportApproved, ReportRejected
│   ├── Exceptions/         - InvalidExpenseTransition, PolicyViolation
│   └── Strategies/
│       ├── ExpenseApproval/          - Auto-approve below threshold, manager approval
│       ├── ReimbursementProcessing/  - Manual vs auto processing
│       ├── ReceiptProcessing/        - Manual upload (OCR stub for future)
│       └── PolicyValidation/         - Validate expense against policies
├── Application/
│   ├── DTOs/               - ExpenseData, ExpenseCategoryData, ExpenseReportData,
│   │                         ExpensePolicyData, ExpenseTagData, ReimbursementData
│   └── UseCases/           - CRUD per entity + SubmitExpense, ApproveExpense,
│                             RejectExpense, SubmitReport
├── Infrastructure/
│   ├── Persistence/        - 6 repository interfaces + Eloquent implementations
│   └── Listeners/          - CreateJournalEntryOnExpenseApproved (→ Accounting),
│                             CreateJournalEntryOnReimbursement (→ Accounting)
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/ - 7 controllers (6 CRUD + Dashboard)
│       └── Requests/       - Store/Update form requests per entity (12 requests)
├── Routes/
│   └── api.php             - All API routes under /tenant/expenses/
├── database/
│   └── migrations/tenant/  - 8 migrations (6 entity + 2 pivot)
└── Providers/
    ├── ExpensesServiceProvider.php     - Repository + strategy bindings
    └── EventServiceProvider.php        - Event listener registrations (incl. cross-module)
```

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/expenses/
├── page.tsx                  - Expenses Dashboard (stats cards)
├── layout.tsx                - Module layout wrapper
├── categories/               - Expense Categories CRUD (SimpleCRUDPage)
├── expenses/                 - Expenses CRUD (SimpleCRUDPage)
├── reports/                  - Expense Reports CRUD (SimpleCRUDPage)
│   └── [id]/                 - Report detail view (grouped expenses, approval actions)
├── policies/                 - Expense Policies CRUD (SimpleCRUDPage)
├── tags/                     - Expense Tags CRUD (SimpleCRUDPage)
└── reimbursements/           - Reimbursements CRUD (SimpleCRUDPage)
```

## API Routes

All routes are prefixed with `/tenant/expenses` and require `auth:api` + `tenant_roles` + `throttle:60,1` middleware.

### Dashboard
- `GET /tenant/expenses/dashboard/stats` - Dashboard statistics
- `GET /tenant/expenses/dashboard/recent-expenses` - Recent expenses

### Categories
- `GET /tenant/expenses/categories` - List categories
- `POST /tenant/expenses/categories` - Create category
- `GET /tenant/expenses/categories/{id}` - Get category
- `PUT /tenant/expenses/categories/{id}` - Update category
- `DELETE /tenant/expenses/categories/{id}` - Delete category
- `POST /tenant/expenses/categories/bulk-destroy` - Bulk delete

### Expenses
- `GET /tenant/expenses/expenses` - List expenses
- `POST /tenant/expenses/expenses` - Create expense
- `GET /tenant/expenses/expenses/{id}` - Get expense
- `PUT /tenant/expenses/expenses/{id}` - Update expense
- `DELETE /tenant/expenses/expenses/{id}` - Delete expense
- `POST /tenant/expenses/expenses/bulk-destroy` - Bulk delete
- `POST /tenant/expenses/expenses/{id}/submit` - Submit for approval (draft → pending)
- `POST /tenant/expenses/expenses/{id}/approve` - Approve expense (pending → approved)
- `POST /tenant/expenses/expenses/{id}/reject` - Reject expense (pending → rejected)

### Expense Reports
- `GET /tenant/expenses/reports` - List reports
- `POST /tenant/expenses/reports` - Create report
- `GET /tenant/expenses/reports/{id}` - Get report
- `PUT /tenant/expenses/reports/{id}` - Update report
- `DELETE /tenant/expenses/reports/{id}` - Delete report
- `POST /tenant/expenses/reports/bulk-destroy` - Bulk delete
- `POST /tenant/expenses/reports/{id}/submit` - Submit report (draft → submitted)
- `POST /tenant/expenses/reports/{id}/approve` - Approve report (submitted → approved)
- `POST /tenant/expenses/reports/{id}/reject` - Reject report (submitted → rejected)

### Policies
- `GET /tenant/expenses/policies` - List policies
- `POST /tenant/expenses/policies` - Create policy
- `GET /tenant/expenses/policies/{id}` - Get policy
- `PUT /tenant/expenses/policies/{id}` - Update policy
- `DELETE /tenant/expenses/policies/{id}` - Delete policy
- `POST /tenant/expenses/policies/bulk-destroy` - Bulk delete

### Tags
- `GET /tenant/expenses/tags` - List tags
- `POST /tenant/expenses/tags` - Create tag
- `GET /tenant/expenses/tags/{id}` - Get tag
- `PUT /tenant/expenses/tags/{id}` - Update tag
- `DELETE /tenant/expenses/tags/{id}` - Delete tag
- `POST /tenant/expenses/tags/bulk-destroy` - Bulk delete

### Reimbursements
- `GET /tenant/expenses/reimbursements` - List reimbursements
- `POST /tenant/expenses/reimbursements` - Create reimbursement
- `GET /tenant/expenses/reimbursements/{id}` - Get reimbursement
- `PUT /tenant/expenses/reimbursements/{id}` - Update reimbursement
- `DELETE /tenant/expenses/reimbursements/{id}` - Delete reimbursement
- `POST /tenant/expenses/reimbursements/bulk-destroy` - Bulk delete
- `POST /tenant/expenses/reimbursements/{id}/process` - Process reimbursement (pending → processing)

## Database Tables

All tables use the `exp_` prefix:

| Table | Description |
|-------|-------------|
| `exp_categories` | Hierarchical expense categories with `default_account_id` for Accounting integration |
| `exp_expenses` | Expense records with status workflow (draft/pending/approved/rejected/reimbursed) |
| `exp_reports` | Expense report headers for batch approval |
| `exp_policies` | Policy rules (max_amount, receipt_required, approval_required, category_restriction) |
| `exp_tags` | Tags for expense categorization |
| `exp_reimbursements` | Reimbursement tracking (pending/processing/completed/failed) |
| `exp_expense_tag` | Pivot table: expense ↔ tag |
| `exp_expense_reimbursement` | Pivot table: expense ↔ reimbursement |

## Permissions

The Expenses module includes ~35 permissions grouped by entity:

- `expenses.dashboard.view`
- `expenses.categories.view/create/edit/delete`
- `expenses.expenses.view/create/edit/delete/submit/approve/reject`
- `expenses.reports.view/create/edit/delete/submit/approve/reject`
- `expenses.policies.view/create/edit/delete`
- `expenses.tags.view/create/edit/delete`
- `expenses.reimbursements.view/create/edit/delete/process`

### Role Assignments

| Permission Set | Admin | Finance Manager | Employee | Viewer |
|---------------|-------|----------------|----------|--------|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Categories (full) | ✅ | ✅ | view | view |
| Expenses (full + submit/approve/reject) | ✅ | ✅ | view/create/submit | view |
| Reports (full + submit/approve/reject) | ✅ | ✅ | view/create/submit | view |
| Policies (full) | ✅ | ✅ | view | view |
| Tags (full) | ✅ | ✅ | view | view |
| Reimbursements (full + process) | ✅ | ✅ | view | view |

## Strategy Pattern

### ExpenseApproval
Determines whether an expense is auto-approved or requires manager approval.
- `DefaultExpenseApprovalStrategy` - Auto-approves below threshold, requires manager approval above

### ReimbursementProcessing
Handles the reimbursement payment workflow.
- `DefaultReimbursementProcessingStrategy` - Marks as processing → completed (stub for payment gateway)

### ReceiptProcessing
Handles receipt upload and processing.
- `DefaultReceiptProcessingStrategy` - Manual upload stub (future: OCR extraction)

### PolicyValidation
Validates expenses against configured policies.
- `DefaultPolicyValidationStrategy` - Validates max_amount, receipt_required policies

## Domain Events

- `ExpenseCreated` - Fired when a new expense is created
- `ExpenseSubmitted` - Fired when an expense is submitted for approval
- `ExpenseApproved` - Fired when an expense is approved (**triggers cross-module Accounting journal entry**)
- `ExpenseRejected` - Fired when an expense is rejected
- `ExpenseReimbursed` - Fired when an expense is reimbursed (**triggers cross-module Accounting journal entry**)
- `ReportSubmitted` - Fired when a report is submitted
- `ReportApproved` - Fired when a report is approved
- `ReportRejected` - Fired when a report is rejected

## Entity State Machines

- **Expense**: `draft` → `pending` → `approved` → `reimbursed` | `rejected` → `pending` | `cancelled`
- **ExpenseReport**: `draft` → `submitted` → `approved` → `reimbursed` | `rejected` → `submitted`
- **Reimbursement**: `pending` → `processing` → `completed` | `failed`

## Cross-Module Integration

The Expenses module sends events to the **Accounting** module:

| Event | Listener | Action |
|-------|----------|--------|
| `ExpenseApproved` | `CreateJournalEntryOnExpenseApproved` | Debit expense account (from `category.default_account_id`), credit cash/AP |
| `ExpenseReimbursed` | `CreateJournalEntryOnReimbursement` | Debit accounts payable, credit cash |

The listeners live in `Modules\Expenses\Infrastructure\Listeners\` and are registered in `Expenses\EventServiceProvider`.

## Installation

1. Run migrations:
```bash
php artisan migrate --path=modules/Expenses/database/migrations/tenant
```

2. Seed permissions:
```bash
php artisan db:seed --class=ExpensesPermissionSeeder
```

3. Clear module cache:
```bash
php artisan config:clear
```

## Development Notes

- All entities use the `exp_` table prefix
- All entities have `custom_fields` JSON column for extensibility
- Tenant-scoped via separate database per tenant (no `tenant_id` columns)
- Rich entities with business methods (e.g., `Expense::canTransitionTo()`, `ExpenseReport::approve()`)
- Repository pattern for persistence abstraction
- UseCase pattern for business logic
- Form Request validation on all store/update endpoints
- `ApiResponseEnvelope` trait for consistent API responses
- `TableListTrait` (`App\Repositories\Traits\TableListTrait`) for server-side pagination, search, and sorting
- Cross-module listeners create Accounting journal entries on expense approval/reimbursement
