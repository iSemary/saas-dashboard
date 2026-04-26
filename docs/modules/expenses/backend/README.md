# Expenses Module — Backend

## Directory Layout

```
backend/modules/Expenses/
├── Domain/
│   ├── Entities/
│   │   ├── ExpenseCategory.php       - Hierarchical categories (parent_id, default_account_id)
│   │   ├── Expense.php               - Core entity (title, amount, status, category_id)
│   │   ├── ExpenseReport.php         - Grouped expenses for approval (title, status, total_amount)
│   │   ├── ExpensePolicy.php         - Policy rules (type, rules, priority, is_active)
│   │   ├── ExpenseTag.php            - Tags (name, color)
│   │   └── Reimbursement.php         - Reimbursement tracking (reference, amount, status)
│   ├── ValueObjects/
│   │   ├── ExpenseStatus.php         - draft, pending, approved, rejected, reimbursed, cancelled
│   │   ├── ReportStatus.php          - draft, submitted, approved, rejected, reimbursed
│   │   ├── PolicyType.php            - max_amount, receipt_required, approval_required, category_restriction
│   │   ├── ReimbursementStatus.php   - pending, processing, completed, failed
│   │   └── ExpenseCurrency.php       - ISO 4217 currency codes
│   ├── Events/
│   │   ├── ExpenseCreated.php
│   │   ├── ExpenseSubmitted.php
│   │   ├── ExpenseApproved.php       - → triggers CreateJournalEntryOnExpenseApproved
│   │   ├── ExpenseRejected.php
│   │   ├── ExpenseReimbursed.php     - → triggers CreateJournalEntryOnReimbursement
│   │   ├── ReportSubmitted.php
│   │   ├── ReportApproved.php
│   │   └── ReportRejected.php
│   ├── Exceptions/
│   │   ├── InvalidExpenseTransition.php
│   │   └── PolicyViolation.php
│   └── Strategies/
│       ├── ExpenseApproval/
│       │   ├── ExpenseApprovalStrategyInterface.php
│       │   └── DefaultExpenseApprovalStrategy.php
│       ├── ReimbursementProcessing/
│       │   ├── ReimbursementProcessingStrategyInterface.php
│       │   └── DefaultReimbursementProcessingStrategy.php
│       ├── ReceiptProcessing/
│       │   ├── ReceiptProcessingStrategyInterface.php
│       │   └── DefaultReceiptProcessingStrategy.php
│       └── PolicyValidation/
│           ├── PolicyValidationStrategyInterface.php
│           └── DefaultPolicyValidationStrategy.php
├── Application/
│   ├── DTOs/
│   │   ├── ExpenseData.php
│   │   ├── ExpenseCategoryData.php
│   │   ├── ExpenseReportData.php
│   │   ├── ExpensePolicyData.php
│   │   ├── ExpenseTagData.php
│   │   └── ReimbursementData.php
│   └── UseCases/
│       ├── SubmitExpense.php
│       ├── ApproveExpense.php
│       ├── RejectExpense.php
│       └── SubmitReport.php
├── Infrastructure/
│   ├── Persistence/
│   │   ├── ExpenseCategoryRepositoryInterface.php
│   │   ├── EloquentExpenseCategoryRepository.php
│   │   ├── ExpenseRepositoryInterface.php
│   │   ├── EloquentExpenseRepository.php
│   │   ├── ExpenseReportRepositoryInterface.php
│   │   ├── EloquentExpenseReportRepository.php
│   │   ├── ExpensePolicyRepositoryInterface.php
│   │   ├── EloquentExpensePolicyRepository.php
│   │   ├── ExpenseTagRepositoryInterface.php
│   │   ├── EloquentExpenseTagRepository.php
│   │   ├── ReimbursementRepositoryInterface.php
│   │   └── EloquentReimbursementRepository.php
│   └── Listeners/
│       ├── CreateJournalEntryOnExpenseApproved.php   - Cross-module → Accounting
│       └── CreateJournalEntryOnReimbursement.php     - Cross-module → Accounting
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/
│       │   ├── ExpensesDashboardController.php
│       │   ├── ExpenseCategoryController.php
│       │   ├── ExpenseController.php
│       │   ├── ExpenseReportController.php
│       │   ├── ExpensePolicyController.php
│       │   ├── ExpenseTagController.php
│       │   └── ReimbursementController.php
│       └── Requests/
│           ├── StoreExpenseCategoryRequest.php
│           ├── UpdateExpenseCategoryRequest.php
│           ├── StoreExpenseRequest.php
│           ├── UpdateExpenseRequest.php
│           ├── StoreExpenseReportRequest.php
│           ├── UpdateExpenseReportRequest.php
│           ├── StoreExpensePolicyRequest.php
│           ├── UpdateExpensePolicyRequest.php
│           ├── StoreExpenseTagRequest.php
│           ├── UpdateExpenseTagRequest.php
│           ├── StoreReimbursementRequest.php
│           └── UpdateReimbursementRequest.php
├── Routes/
│   └── api.php
├── database/
│   └── migrations/tenant/
│       ├── 2024_01_01_000001_create_exp_categories_table.php
│       ├── 2024_01_01_000002_create_exp_expenses_table.php
│       ├── 2024_01_01_000003_create_exp_reports_table.php
│       ├── 2024_01_01_000004_create_exp_policies_table.php
│       ├── 2024_01_01_000005_create_exp_tags_table.php
│       ├── 2024_01_01_000006_create_exp_reimbursements_table.php
│       ├── 2024_01_01_000007_create_exp_expense_tag_pivot.php
│       └── 2024_01_01_000008_create_exp_expense_reimbursement_pivot.php
└── Providers/
    ├── ExpensesServiceProvider.php
    └── EventServiceProvider.php
```

## Key Design Decisions

1. **Approval Workflow**: Expenses follow a state machine (draft → pending → approved/rejected) with strategy-based approval logic
2. **Policy Validation**: `DefaultPolicyValidationStrategy` validates expenses against configured policies before approval
3. **Cross-Module Integration**: `ExpenseApproved` and `ExpenseReimbursed` events trigger Accounting journal entries via listeners in the Expenses module
4. **Account Mapping**: `ExpenseCategory.default_account_id` maps expense categories to Accounting chart of accounts
5. **Tag System**: Many-to-many relationship between expenses and tags via `exp_expense_tag` pivot
6. **Reimbursement Linking**: Many-to-many relationship between expenses and reimbursements via `exp_expense_reimbursement` pivot
7. **Form Requests**: All store/update endpoints use dedicated Form Request classes with `$request->validated()`
8. **TableListTrait**: Server-side pagination, search, and sorting via `App\Repositories\Traits\TableListTrait`

## Cross-Module Event Flow

```
Expense Approved ─────────────────────────────────────────────────────┐
  │                                                                   │
  ▼                                                                   │
CreateJournalEntryOnExpenseApproved                                   │
  │                                                                   │
  ├── Debit:  category.default_account_id (expense account)           │
  └── Credit: cash or accounts payable                                │
                                                                      │
Expense Reimbursed ───────────────────────────────────────────────────┤
  │                                                                   │
  ▼                                                                   │
CreateJournalEntryOnReimbursement                                     │
  │                                                                   │
  ├── Debit:  accounts payable                                        │
  └── Credit: cash                                                    │
                                                                      ▼
                    Accounting Module (acc_journal_entries)
```

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
