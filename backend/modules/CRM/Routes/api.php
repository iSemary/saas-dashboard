<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Api\CompanyApiController;
use Modules\CRM\Http\Controllers\Api\ContactApiController;
use Modules\CRM\Http\Controllers\Api\CrmApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\LeadApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\OpportunityApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\ActivityApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmNoteApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmFileApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmPipelineStageApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmAutomationRuleApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmWebhookApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmImportJobApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmReportApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmSearchApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmEmailApiController;
use Modules\CRM\Presentation\Http\Controllers\Api\CrmAuditApiController;

// ─── CRM API Routes ──────────────────────────────────────────────
Route::middleware(['auth:api', 'tenant_roles'])->prefix('tenant/crm')->group(function () {
    // Lead routes
    Route::prefix('leads')->group(function () {
        Route::get('/', [LeadApiController::class, 'index'])->name('api.crm.leads.index');
        Route::post('/', [LeadApiController::class, 'store'])->name('api.crm.leads.store');
        Route::get('/{id}', [LeadApiController::class, 'show'])->name('api.crm.leads.show');
        Route::put('/{id}', [LeadApiController::class, 'update'])->name('api.crm.leads.update');
        Route::delete('/{id}', [LeadApiController::class, 'destroy'])->name('api.crm.leads.destroy');
        Route::post('/{id}/convert', [LeadApiController::class, 'convert'])->name('api.crm.leads.convert');
    });

    // Opportunity routes
    Route::prefix('opportunities')->group(function () {
        Route::get('/', [OpportunityApiController::class, 'index'])->name('api.crm.opportunities.index');
        Route::post('/', [OpportunityApiController::class, 'store'])->name('api.crm.opportunities.store');
        Route::get('/{id}', [OpportunityApiController::class, 'show'])->name('api.crm.opportunities.show');
        Route::put('/{id}', [OpportunityApiController::class, 'update'])->name('api.crm.opportunities.update');
        Route::delete('/{id}', [OpportunityApiController::class, 'destroy'])->name('api.crm.opportunities.destroy');
        Route::get('/pipeline/data', [OpportunityApiController::class, 'pipeline'])->name('api.crm.opportunities.pipeline');
        Route::post('/{id}/move-stage', [OpportunityApiController::class, 'moveStage'])->name('api.crm.opportunities.move-stage');
        Route::post('/{id}/close-won', [OpportunityApiController::class, 'closeWon'])->name('api.crm.opportunities.close-won');
    });

    // Activity routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityApiController::class, 'index'])->name('api.crm.activities.index');
        Route::post('/', [ActivityApiController::class, 'store'])->name('api.crm.activities.store');
        Route::get('/{id}', [ActivityApiController::class, 'show'])->name('api.crm.activities.show');
        Route::put('/{id}', [ActivityApiController::class, 'update'])->name('api.crm.activities.update');
        Route::delete('/{id}', [ActivityApiController::class, 'destroy'])->name('api.crm.activities.destroy');
        Route::post('/{id}/complete', [ActivityApiController::class, 'complete'])->name('api.crm.activities.complete');
        Route::get('/upcoming/list', [ActivityApiController::class, 'upcoming'])->name('api.crm.activities.upcoming');
        Route::get('/overdue/list', [ActivityApiController::class, 'overdue'])->name('api.crm.activities.overdue');
    });

    // Company routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyApiController::class, 'index'])->name('api.crm.companies.index');
        Route::post('/', [CompanyApiController::class, 'store'])->name('api.crm.companies.store');
        Route::get('/{id}', [CompanyApiController::class, 'show'])->name('api.crm.companies.show');
        Route::put('/{id}', [CompanyApiController::class, 'update'])->name('api.crm.companies.update');
        Route::delete('/{id}', [CompanyApiController::class, 'destroy'])->name('api.crm.companies.destroy');
        Route::get('/{id}/activity', [CompanyApiController::class, 'activity'])->name('api.crm.companies.activity');
        Route::post('/bulk-delete', [CompanyApiController::class, 'bulkDelete'])->name('api.crm.companies.bulk-delete');
    });

    // Contact routes
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactApiController::class, 'index'])->name('api.crm.contacts.index');
        Route::post('/', [ContactApiController::class, 'store'])->name('api.crm.contacts.store');
        Route::get('/{id}', [ContactApiController::class, 'show'])->name('api.crm.contacts.show');
        Route::put('/{id}', [ContactApiController::class, 'update'])->name('api.crm.contacts.update');
        Route::delete('/{id}', [ContactApiController::class, 'destroy'])->name('api.crm.contacts.destroy');
        Route::get('/{id}/activity', [ContactApiController::class, 'activity'])->name('api.crm.contacts.activity');
        Route::post('/bulk-delete', [ContactApiController::class, 'bulkDelete'])->name('api.crm.contacts.bulk-delete');
    });

    // Notes routes
    Route::prefix('notes')->group(function () {
        Route::get('/', [CrmNoteApiController::class, 'index'])->name('api.crm.notes.index');
        Route::post('/', [CrmNoteApiController::class, 'store'])->name('api.crm.notes.store');
        Route::get('/{id}', [CrmNoteApiController::class, 'show'])->name('api.crm.notes.show');
        Route::put('/{id}', [CrmNoteApiController::class, 'update'])->name('api.crm.notes.update');
        Route::delete('/{id}', [CrmNoteApiController::class, 'destroy'])->name('api.crm.notes.destroy');
        Route::get('/related/{type}/{id}', [CrmNoteApiController::class, 'getForRelated'])->name('api.crm.notes.related');
    });

    // Files routes
    Route::prefix('files')->group(function () {
        Route::get('/', [CrmFileApiController::class, 'index'])->name('api.crm.files.index');
        Route::post('/', [CrmFileApiController::class, 'store'])->name('api.crm.files.store');
        Route::get('/{id}', [CrmFileApiController::class, 'show'])->name('api.crm.files.show');
        Route::delete('/{id}', [CrmFileApiController::class, 'destroy'])->name('api.crm.files.destroy');
        Route::get('/{id}/download', [CrmFileApiController::class, 'download'])->name('api.crm.files.download');
        Route::get('/related/{type}/{id}', [CrmFileApiController::class, 'getForRelated'])->name('api.crm.files.related');
    });

    // Pipeline Stage routes
    Route::prefix('pipeline-stages')->group(function () {
        Route::get('/', [CrmPipelineStageApiController::class, 'index'])->name('api.crm.pipeline-stages.index');
        Route::post('/', [CrmPipelineStageApiController::class, 'store'])->name('api.crm.pipeline-stages.store');
        Route::get('/{id}', [CrmPipelineStageApiController::class, 'show'])->name('api.crm.pipeline-stages.show');
        Route::put('/{id}', [CrmPipelineStageApiController::class, 'update'])->name('api.crm.pipeline-stages.update');
        Route::delete('/{id}', [CrmPipelineStageApiController::class, 'destroy'])->name('api.crm.pipeline-stages.destroy');
        Route::post('/reorder', [CrmPipelineStageApiController::class, 'reorder'])->name('api.crm.pipeline-stages.reorder');
    });

    // Automation Rules routes
    Route::prefix('automation-rules')->group(function () {
        Route::get('/', [CrmAutomationRuleApiController::class, 'index'])->name('api.crm.automation-rules.index');
        Route::post('/', [CrmAutomationRuleApiController::class, 'store'])->name('api.crm.automation-rules.store');
        Route::get('/{id}', [CrmAutomationRuleApiController::class, 'show'])->name('api.crm.automation-rules.show');
        Route::put('/{id}', [CrmAutomationRuleApiController::class, 'update'])->name('api.crm.automation-rules.update');
        Route::delete('/{id}', [CrmAutomationRuleApiController::class, 'destroy'])->name('api.crm.automation-rules.destroy');
        Route::post('/{id}/toggle', [CrmAutomationRuleApiController::class, 'toggle'])->name('api.crm.automation-rules.toggle');
    });

    // Webhooks routes
    Route::prefix('webhooks')->group(function () {
        Route::get('/', [CrmWebhookApiController::class, 'index'])->name('api.crm.webhooks.index');
        Route::post('/', [CrmWebhookApiController::class, 'store'])->name('api.crm.webhooks.store');
        Route::get('/{id}', [CrmWebhookApiController::class, 'show'])->name('api.crm.webhooks.show');
        Route::put('/{id}', [CrmWebhookApiController::class, 'update'])->name('api.crm.webhooks.update');
        Route::delete('/{id}', [CrmWebhookApiController::class, 'destroy'])->name('api.crm.webhooks.destroy');
        Route::post('/{id}/toggle', [CrmWebhookApiController::class, 'toggle'])->name('api.crm.webhooks.toggle');
        Route::post('/{id}/regenerate-secret', [CrmWebhookApiController::class, 'regenerateSecret'])->name('api.crm.webhooks.regenerate-secret');
    });

    // Import Jobs routes
    Route::prefix('import-jobs')->group(function () {
        Route::get('/', [CrmImportJobApiController::class, 'index'])->name('api.crm.import-jobs.index');
        Route::post('/', [CrmImportJobApiController::class, 'store'])->name('api.crm.import-jobs.store');
        Route::get('/{id}', [CrmImportJobApiController::class, 'show'])->name('api.crm.import-jobs.show');
        Route::delete('/{id}', [CrmImportJobApiController::class, 'destroy'])->name('api.crm.import-jobs.destroy');
        Route::get('/template/{entityType}', [CrmImportJobApiController::class, 'downloadTemplate'])->name('api.crm.import-jobs.template');
    });

    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/pipeline', [CrmReportApiController::class, 'pipeline'])->name('api.crm.reports.pipeline');
        Route::get('/conversion', [CrmReportApiController::class, 'conversion'])->name('api.crm.reports.conversion');
        Route::get('/activity', [CrmReportApiController::class, 'activity'])->name('api.crm.reports.activity');
        Route::get('/leads-by-source', [CrmReportApiController::class, 'leadsBySource'])->name('api.crm.reports.leads-by-source');
        Route::get('/monthly-trends', [CrmReportApiController::class, 'monthlyTrends'])->name('api.crm.reports.monthly-trends');
        Route::get('/overview', [CrmReportApiController::class, 'overview'])->name('api.crm.reports.overview');
    });

    // Search route
    Route::get('/search', [CrmSearchApiController::class, 'search'])->name('api.crm.search');

    // Email routes
    Route::prefix('emails')->group(function () {
        Route::get('/', [CrmEmailApiController::class, 'index'])->name('api.crm.emails.index');
        Route::post('/log', [CrmEmailApiController::class, 'log'])->name('api.crm.emails.log');
        Route::post('/send', [CrmEmailApiController::class, 'send'])->name('api.crm.emails.send');
    });

    // Audit route
    Route::get('/audit', [CrmAuditApiController::class, 'index'])->name('api.crm.audit');

    // Dashboard summary
    Route::get('/dashboard', [CrmApiController::class, 'index'])->name('api.crm.dashboard');
});

// ─── Tenant CRM Module (legacy dashboard endpoint) ──────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('modules/crm', [CrmApiController::class, 'index'])->name('modules.crm');
});
