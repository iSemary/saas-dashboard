<?php

use Illuminate\Support\Facades\Route;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmCampaignApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmTemplateApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmContactApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmContactListApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmCredentialApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmAutomationRuleApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmWebhookApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmAbTestApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmImportJobApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmSendingLogApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmOptOutApiController;
use Modules\SmsMarketing\Presentation\Http\Controllers\Api\SmDashboardApiController;

Route::middleware(['auth:api', 'tenant_roles'])->prefix('tenant/sms-marketing')->group(function () {

    // Dashboard
    Route::get('dashboard/stats', [SmDashboardApiController::class, 'stats']);
    Route::get('dashboard/recent-campaigns', [SmDashboardApiController::class, 'recentCampaigns']);

    // Campaigns
    Route::apiResource('campaigns', SmCampaignApiController::class);
    Route::post('campaigns/bulk-destroy', [SmCampaignApiController::class, 'bulkDelete']);
    Route::post('campaigns/{id}/send', [SmCampaignApiController::class, 'send']);
    Route::post('campaigns/{id}/schedule', [SmCampaignApiController::class, 'schedule']);
    Route::post('campaigns/{id}/pause', [SmCampaignApiController::class, 'pause']);
    Route::post('campaigns/{id}/cancel', [SmCampaignApiController::class, 'cancel']);

    // Templates
    Route::apiResource('templates', SmTemplateApiController::class);
    Route::post('templates/bulk-destroy', [SmTemplateApiController::class, 'bulkDelete']);

    // Contacts
    Route::apiResource('contacts', SmContactApiController::class);
    Route::post('contacts/bulk-destroy', [SmContactApiController::class, 'bulkDelete']);

    // Contact Lists
    Route::apiResource('contact-lists', SmContactListApiController::class);
    Route::post('contact-lists/bulk-destroy', [SmContactListApiController::class, 'bulkDelete']);
    Route::post('contact-lists/{id}/add-contacts', [SmContactListApiController::class, 'addContacts']);
    Route::post('contact-lists/{id}/remove-contacts', [SmContactListApiController::class, 'removeContacts']);

    // Credentials
    Route::apiResource('credentials', SmCredentialApiController::class);
    Route::post('credentials/bulk-destroy', [SmCredentialApiController::class, 'bulkDelete']);

    // Automation Rules
    Route::apiResource('automation-rules', SmAutomationRuleApiController::class);
    Route::post('automation-rules/bulk-destroy', [SmAutomationRuleApiController::class, 'bulkDelete']);
    Route::post('automation-rules/{id}/toggle', [SmAutomationRuleApiController::class, 'toggle']);

    // Webhooks
    Route::apiResource('webhooks', SmWebhookApiController::class);
    Route::post('webhooks/bulk-destroy', [SmWebhookApiController::class, 'bulkDelete']);

    // A/B Tests
    Route::apiResource('ab-tests', SmAbTestApiController::class);
    Route::post('ab-tests/bulk-destroy', [SmAbTestApiController::class, 'bulkDelete']);
    Route::post('ab-tests/{id}/select-winner', [SmAbTestApiController::class, 'selectWinner']);

    // Import Jobs
    Route::apiResource('import-jobs', SmImportJobApiController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('import-jobs/bulk-destroy', [SmImportJobApiController::class, 'bulkDelete']);
    Route::post('import-jobs/{id}/process', [SmImportJobApiController::class, 'process']);

    // Sending Logs (read-only)
    Route::apiResource('sending-logs', SmSendingLogApiController::class)->only(['index', 'show']);

    // Opt-Outs
    Route::apiResource('opt-outs', SmOptOutApiController::class)->only(['index', 'store']);
});
