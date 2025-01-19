<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\EmailController;
use Modules\Email\Http\Controllers\EmailTemplateController;
use Modules\Email\Http\Controllers\EmailSubscriberController;
use Modules\Email\Http\Controllers\EmailCampaignController;
use Modules\Email\Http\Controllers\EmailRecipientController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class)->names('email-templates');

    // Email Recipients
    Route::resource('email-recipients', EmailRecipientController::class)->names('email-recipients');

    // Email Campaigns
    Route::resource('email-campaigns', EmailCampaignController::class)->names('email-campaigns');

    // Email Subscribers
    Route::get('email-subscribers', [EmailSubscriberController::class, "index"])->name('email-subscribers.index');

    // Email Routes
    Route::post('emails/resend/{id}', [EmailController::class, "resend"])->name('emails.resend');
    Route::get('emails/compose', [EmailController::class, "compose"])->name('emails.compose');
    Route::get('emails', [EmailController::class, "index"])->name('emails.index');
});
