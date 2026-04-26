<?php

use Illuminate\Support\Facades\Route;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpenseCategoryController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpenseController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpenseReportController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpensePolicyController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpenseTagController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ReimbursementController;
use Modules\Expenses\Presentation\Http\Controllers\Api\ExpensesDashboardController;

Route::middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->prefix('tenant/expenses')->group(function () {

    // Dashboard
    Route::get('dashboard/stats', [ExpensesDashboardController::class, 'stats']);
    Route::get('dashboard/recent-expenses', [ExpensesDashboardController::class, 'recentExpenses']);

    // Categories
    Route::apiResource('categories', ExpenseCategoryController::class);
    Route::post('categories/bulk-destroy', [ExpenseCategoryController::class, 'bulkDestroy']);

    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('expenses/bulk-destroy', [ExpenseController::class, 'bulkDestroy']);
    Route::post('expenses/{id}/submit', [ExpenseController::class, 'submit']);
    Route::post('expenses/{id}/approve', [ExpenseController::class, 'approve']);
    Route::post('expenses/{id}/reject', [ExpenseController::class, 'reject']);

    // Reports
    Route::apiResource('reports', ExpenseReportController::class);
    Route::post('reports/bulk-destroy', [ExpenseReportController::class, 'bulkDestroy']);
    Route::post('reports/{id}/submit', [ExpenseReportController::class, 'submit']);
    Route::post('reports/{id}/approve', [ExpenseReportController::class, 'approve']);
    Route::post('reports/{id}/reject', [ExpenseReportController::class, 'reject']);

    // Policies
    Route::apiResource('policies', ExpensePolicyController::class);
    Route::post('policies/bulk-destroy', [ExpensePolicyController::class, 'bulkDestroy']);

    // Tags
    Route::apiResource('tags', ExpenseTagController::class);
    Route::post('tags/bulk-destroy', [ExpenseTagController::class, 'bulkDestroy']);

    // Reimbursements
    Route::apiResource('reimbursements', ReimbursementController::class);
    Route::post('reimbursements/bulk-destroy', [ReimbursementController::class, 'bulkDestroy']);
    Route::post('reimbursements/{id}/process', [ReimbursementController::class, 'process']);
});
