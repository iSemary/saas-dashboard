<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\Api\EmailCampaignApiController;
use Modules\Email\Http\Controllers\Api\EmailTemplateApiController;
use Modules\Email\Http\Controllers\Api\EmailCredentialApiController;
use Modules\Email\Http\Controllers\Api\EmailRecipientApiController;
use Modules\Email\Http\Controllers\Api\EmailGroupApiController;
use Modules\Email\Http\Controllers\Api\EmailSubscriberApiController;
use Modules\Email\Http\Controllers\Api\EmailLogApiController;
use Modules\Email\Http\Controllers\Api\ComposeEmailApiController;

// ─── Landlord Email / Mailing ───────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::prefix('email-campaigns')->name('email-campaigns.')->group(function () {
        Route::get('/', [EmailCampaignApiController::class, 'index'])->name('index');
        Route::post('/', [EmailCampaignApiController::class, 'store'])->name('store');
        Route::delete('/{id}', [EmailCampaignApiController::class, 'destroy'])->name('destroy');
    });
    Route::apiResource('email-templates', EmailTemplateApiController::class);
    Route::apiResource('email-credentials', EmailCredentialApiController::class);
    Route::apiResource('email-recipients', EmailRecipientApiController::class);
    Route::apiResource('email-groups', EmailGroupApiController::class);
    Route::apiResource('email-subscribers', EmailSubscriberApiController::class);
    Route::apiResource('email-log', EmailLogApiController::class)->only(['index', 'destroy']);
    Route::post('compose-email', [ComposeEmailApiController::class, 'send'])->name('compose-email.send');
});
