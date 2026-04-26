<?php

use Illuminate\Support\Facades\Route;
use Modules\ProjectManagement\Presentation\Http\Controllers\Api;

// ==========================================
// Authenticated Routes (Tenant API)
// ==========================================
Route::middleware(['auth:api', 'tenant_roles'])
    ->prefix('tenant/project-management')
    ->name('tenant.project-management.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [Api\ProjectManagementDashboardController::class, 'index'])->name('dashboard');

        // Workspaces
        Route::apiResource('workspaces', Api\WorkspaceController::class);

        // Projects
        Route::apiResource('projects', Api\ProjectController::class);
        Route::post('projects/{id}/archive', [Api\ProjectController::class, 'archive'])->name('projects.archive');
        Route::post('projects/{id}/pause', [Api\ProjectController::class, 'pause'])->name('projects.pause');
        Route::post('projects/{id}/complete', [Api\ProjectController::class, 'complete'])->name('projects.complete');
        Route::post('projects/{id}/health', [Api\ProjectController::class, 'recalculateHealth'])->name('projects.health');
        Route::post('projects/{id}/create-from-template', [Api\ProjectController::class, 'createFromTemplate'])->name('projects.create-from-template');

        // Milestones
        Route::apiResource('projects.milestones', Api\MilestoneController::class);

        // Tasks
        Route::apiResource('projects.tasks', Api\TaskController::class);
        Route::post('tasks/{id}/move', [Api\TaskController::class, 'move'])->name('tasks.move');
        Route::post('tasks/{id}/reorder', [Api\TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::post('tasks/{id}/labels', [Api\TaskController::class, 'attachLabels'])->name('tasks.labels.attach');
        Route::delete('tasks/{id}/labels', [Api\TaskController::class, 'detachLabels'])->name('tasks.labels.detach');

        // Task Dependencies
        Route::apiResource('tasks.dependencies', Api\TaskDependencyController::class)->only(['index', 'store', 'destroy']);

        // Board
        Route::get('projects/{projectId}/board', [Api\BoardController::class, 'show'])->name('board.show');
        Route::put('projects/{projectId}/board/configure', [Api\BoardController::class, 'configure'])->name('board.configure');

        // Board Columns
        Route::apiResource('projects.board-columns', Api\BoardColumnController::class);
        Route::post('projects/{projectId}/board-columns/reorder', [Api\BoardColumnController::class, 'reorder'])->name('board-columns.reorder');

        // Board Swimlanes
        Route::apiResource('projects.board-swimlanes', Api\BoardSwimlaneController::class);
        Route::post('projects/{projectId}/board-swimlanes/reorder', [Api\BoardSwimlaneController::class, 'reorder'])->name('board-swimlanes.reorder');

        // Task Labels
        Route::apiResource('projects.labels', Api\TaskLabelController::class);

        // Sprint Cycles
        Route::apiResource('projects.sprint-cycles', Api\SprintCycleController::class);

        // Project Members
        Route::apiResource('projects.members', Api\ProjectMemberController::class);

        // Project Templates
        Route::apiResource('templates', Api\ProjectTemplateController::class);

        // Risks
        Route::apiResource('projects.risks', Api\ProjectRiskController::class);

        // Issues
        Route::apiResource('projects.issues', Api\ProjectIssueController::class);
        Route::post('issues/{id}/promote-to-task', [Api\ProjectIssueController::class, 'promoteToTask'])->name('issues.promote-to-task');

        // Comments
        Route::apiResource('projects.comments', Api\ProjectCommentController::class)->only(['index', 'store', 'destroy']);

        // Webhooks
        Route::apiResource('projects.webhooks', Api\ProjectWebhookController::class);
        Route::post('webhooks/{id}/toggle', [Api\ProjectWebhookController::class, 'toggle'])->name('webhooks.toggle');
        Route::post('webhooks/{id}/regenerate-secret', [Api\ProjectWebhookController::class, 'regenerateSecret'])->name('webhooks.regenerate-secret');

        // Reports
        Route::get('reports/throughput', [Api\ProjectReportController::class, 'throughput'])->name('reports.throughput');
        Route::get('reports/overdue', [Api\ProjectReportController::class, 'overdue'])->name('reports.overdue');
        Route::get('reports/workload', [Api\ProjectReportController::class, 'workload'])->name('reports.workload');
        Route::get('reports/health', [Api\ProjectReportController::class, 'health'])->name('reports.health');
    });
