<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Presentation\Http\Controllers\Api\ChartOfAccountController;
use Modules\Accounting\Presentation\Http\Controllers\Api\JournalEntryController;
use Modules\Accounting\Presentation\Http\Controllers\Api\FiscalYearController;
use Modules\Accounting\Presentation\Http\Controllers\Api\BudgetController;
use Modules\Accounting\Presentation\Http\Controllers\Api\TaxRateController;
use Modules\Accounting\Presentation\Http\Controllers\Api\BankAccountController;
use Modules\Accounting\Presentation\Http\Controllers\Api\BankTransactionController;
use Modules\Accounting\Presentation\Http\Controllers\Api\ReconciliationController;
use Modules\Accounting\Presentation\Http\Controllers\Api\AccountingDashboardController;
use Modules\Accounting\Presentation\Http\Controllers\Api\ReportController;

Route::middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->prefix('tenant/accounting')->group(function () {

    // Dashboard
    Route::get('dashboard/stats', [AccountingDashboardController::class, 'stats']);
    Route::get('dashboard/recent-entries', [AccountingDashboardController::class, 'recentEntries']);
    Route::get('dashboard/account-balances', [AccountingDashboardController::class, 'accountBalances']);

    // Reports
    Route::get('reports/{type}', [ReportController::class, 'generate']);

    // Chart of Accounts
    Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
    Route::post('chart-of-accounts/bulk-destroy', [ChartOfAccountController::class, 'bulkDestroy']);

    // Journal Entries
    Route::apiResource('journal-entries', JournalEntryController::class);
    Route::post('journal-entries/bulk-destroy', [JournalEntryController::class, 'bulkDestroy']);
    Route::post('journal-entries/{id}/post', [JournalEntryController::class, 'post']);
    Route::post('journal-entries/{id}/cancel', [JournalEntryController::class, 'cancel']);

    // Fiscal Years
    Route::apiResource('fiscal-years', FiscalYearController::class);
    Route::post('fiscal-years/{id}/close', [FiscalYearController::class, 'close']);

    // Budgets
    Route::apiResource('budgets', BudgetController::class);
    Route::post('budgets/bulk-destroy', [BudgetController::class, 'bulkDestroy']);

    // Tax Rates
    Route::apiResource('tax-rates', TaxRateController::class);
    Route::post('tax-rates/bulk-destroy', [TaxRateController::class, 'bulkDestroy']);

    // Bank Accounts
    Route::apiResource('bank-accounts', BankAccountController::class);
    Route::post('bank-accounts/bulk-destroy', [BankAccountController::class, 'bulkDestroy']);

    // Bank Transactions
    Route::apiResource('bank-transactions', BankTransactionController::class);
    Route::post('bank-transactions/bulk-destroy', [BankTransactionController::class, 'bulkDestroy']);

    // Reconciliation
    Route::apiResource('reconciliations', ReconciliationController::class);
    Route::post('reconciliations/{id}/complete', [ReconciliationController::class, 'complete']);
});
