<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmCampaignApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmTemplateApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmContactApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmContactListApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmCredentialApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmAutomationRuleApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmWebhookApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmAbTestApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmImportJobApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmSendingLogApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmUnsubscribeApiController;
use Modules\EmailMarketing\Presentation\Http\Controllers\Api\EmDashboardApiController;

Route::middleware(['auth:api', 'tenant_roles'])->prefix('tenant/email-marketing')->group(function () {

    // Dashboard
    Route::get('dashboard/stats', [EmDashboardApiController::class, 'stats']);
    Route::get('dashboard/recent-campaigns', [EmDashboardApiController::class, 'recentCampaigns']);

    // Campaigns
    Route::apiResource('campaigns', EmCampaignApiController::class);
    Route::post('campaigns/bulk-destroy', [EmCampaignApiController::class, 'bulkDelete']);
    Route::post('campaigns/{id}/send', [EmCampaignApiController::class, 'send']);
    Route::post('campaigns/{id}/schedule', [EmCampaignApiController::class, 'schedule']);
    Route::post('campaigns/{id}/pause', [EmCampaignApiController::class, 'pause']);
    Route::post('campaigns/{id}/cancel', [EmCampaignApiController::class, 'cancel']);

    // Templates
    Route::apiResource('templates', EmTemplateApiController::class);
    Route::post('templates/bulk-destroy', [EmTemplateApiController::class, 'bulkDelete']);

    // Contacts
    Route::apiResource('contacts', EmContactApiController::class);
    Route::post('contacts/bulk-destroy', [EmContactApiController::class, 'bulkDelete']);

    // Contact Lists
    Route::apiResource('contact-lists', EmContactListApiController::class);
    Route::post('contact-lists/bulk-destroy', [EmContactListApiController::class, 'bulkDelete']);
    Route::post('contact-lists/{id}/add-contacts', [EmContactListApiController::class, 'addContacts']);
    Route::post('contact-lists/{id}/remove-contacts', [EmContactListApiController::class, 'removeContacts']);

    // Credentials
    Route::apiResource('credentials', EmCredentialApiController::class);
    Route::post('credentials/bulk-destroy', [EmCredentialApiController::class, 'bulkDelete']);

    // Automation Rules
    Route::apiResource('automation-rules', EmAutomationRuleApiController::class);
    Route::post('automation-rules/bulk-destroy', [EmAutomationRuleApiController::class, 'bulkDelete']);
    Route::post('automation-rules/{id}/toggle', [EmAutomationRuleApiController::class, 'toggle']);

    // Webhooks
    Route::apiResource('webhooks', EmWebhookApiController::class);
    Route::post('webhooks/bulk-destroy', [EmWebhookApiController::class, 'bulkDelete']);

    // A/B Tests
    Route::apiResource('ab-tests', EmAbTestApiController::class);
    Route::post('ab-tests/bulk-destroy', [EmAbTestApiController::class, 'bulkDelete']);
    Route::post('ab-tests/{id}/select-winner', [EmAbTestApiController::class, 'selectWinner']);

    // Import Jobs
    Route::apiResource('import-jobs', EmImportJobApiController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('import-jobs/bulk-destroy', [EmImportJobApiController::class, 'bulkDelete']);
    Route::post('import-jobs/{id}/process', [EmImportJobApiController::class, 'process']);

    // Sending Logs (read-only)
    Route::apiResource('sending-logs', EmSendingLogApiController::class)->only(['index', 'show']);

    // Unsubscribes
    Route::apiResource('unsubscribes', EmUnsubscribeApiController::class)->only(['index', 'store']);
});
