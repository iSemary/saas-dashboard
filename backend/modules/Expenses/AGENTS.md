# Expenses Module — Developer Guide

## Overview
Tenant-level Expense Management module using DDD + Strategy Pattern. Manages expense categories, expenses, expense reports, policies, tags, and reimbursements with approval workflows.

## Architecture

```
Domain/          Pure business logic
  Entities/      ExpenseCategory, Expense, ExpenseReport, ExpensePolicy, ExpenseTag, Reimbursement
  ValueObjects/  ExpenseStatus, ReportStatus, PolicyType, ReimbursementStatus, ExpenseCurrency
  Events/        ExpenseCreated, ExpenseSubmitted, ExpenseApproved, ExpenseRejected, ExpenseReimbursed, ReportSubmitted, ReportApproved, ReportRejected
  Strategies/    ExpenseApproval (DefaultExpenseApprovalStrategy), ReimbursementProcessing (DefaultReimbursementProcessingStrategy), ReceiptProcessing (DefaultReceiptProcessingStrategy), PolicyValidation (DefaultPolicyValidationStrategy)
  Exceptions/    InvalidExpenseTransition, PolicyViolation

Application/
  DTOs/          ExpenseData, ExpenseCategoryData, ExpenseReportData, ExpensePolicyData, ExpenseTagData, ReimbursementData
  UseCases/      Create/Update/Delete per entity + SubmitExpense, ApproveExpense, RejectExpense, SubmitReport

Infrastructure/
  Persistence/   Repository interfaces + Eloquent implementations (6 entities)
  Listeners/     CreateJournalEntryOnExpenseApproved, CreateJournalEntryOnReimbursement (cross-module → Accounting)
  Integrations/  (future: payment gateway, OCR receipt processing)

Presentation/
  Http/Controllers/Api/  ExpenseCategoryController, ExpenseController, ExpenseReportController, ExpensePolicyController, ExpenseTagController, ReimbursementController, ExpensesDashboardController
  Http/Requests/         Store/Update form requests per entity
```

## Route Prefix
`/tenant/expenses/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `exp_` prefix: `exp_categories`, `exp_expenses`, `exp_reports`, `exp_policies`, `exp_tags`, `exp_reimbursements`, `exp_expense_tag`, `exp_expense_reimbursement`

## Strategy Pattern
- **ExpenseApproval**: Default auto-approves below threshold, requires manager approval above
- **ReimbursementProcessing**: Default marks as processing → completed (stub for payment gateway)
- **ReceiptProcessing**: Default stub (future: OCR extraction)
- **PolicyValidation**: Default validates max_amount, receipt_required policies

## Cross-Module Integration
- ExpenseApproved event → CreateJournalEntryOnExpenseApproved listener → Accounting journal entry (debit expense, credit cash)
- ExpenseReimbursed event → CreateJournalEntryOnReimbursement listener → Accounting journal entry (debit AP, credit cash)

## Entity State Machines
- **Expense**: draft → pending → approved → reimbursed | rejected → pending | cancelled
- **ExpenseReport**: draft → submitted → approved → reimbursed | rejected → submitted
- **Reimbursement**: pending → processing → completed | failed

## Frontend Pages
- `/dashboard/modules/expenses/categories` — SimpleCRUDPage
- `/dashboard/modules/expenses/expenses` — SimpleCRUDPage
- `/dashboard/modules/expenses/reports` — SimpleCRUDPage
- `/dashboard/modules/expenses/policies` — SimpleCRUDPage
- `/dashboard/modules/expenses/tags` — SimpleCRUDPage
- `/dashboard/modules/expenses/reimbursements` — SimpleCRUDPage
