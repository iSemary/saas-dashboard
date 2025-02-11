<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\EmailController;
use Modules\Email\Http\Controllers\EmailTemplateController;
use Modules\Email\Http\Controllers\EmailSubscriberController;
use Modules\Email\Http\Controllers\EmailCampaignController;
use Modules\Email\Http\Controllers\EmailCredentialController;
use Modules\Email\Http\Controllers\EmailRecipientController;
use Modules\Email\Http\Controllers\EmailGroupController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class)->names('email-templates');

    // Email Recipients
    Route::get('email-recipients/list', [EmailRecipientController::class, "list"])->name('email-recipients.list');
    Route::get('email-recipients/groups/{id}', [EmailRecipientController::class, "groups"])->name('email-recipients.groups');
    Route::post('email-recipients/groups/{id}', [EmailRecipientController::class, "assignGroups"])->name('email-recipients.assignGroups');
    Route::resource('email-recipients', EmailRecipientController::class)->names('email-recipients');
    
    // Email Groups 
    Route::get('email-groups/list', [EmailGroupController::class, "list"])->name('email-groups.list');
    Route::resource('email-groups', EmailGroupController::class)->names('email-groups');
    
    // Email Credentials
    Route::resource('email-credentials', EmailCredentialController::class)->names('email-credentials');

    // Email Campaigns
    Route::resource('email-campaigns', EmailCampaignController::class)->names('email-campaigns');

    // Email Subscribers
    Route::resource('email-subscribers', EmailSubscriberController::class)->names('email-subscribers')->only(['index', 'update', 'edit']);

    // Email Routes
    Route::post('emails/resend/{id}', [EmailController::class, "resend"])->name('emails.resend');
    Route::post('emails/send', [EmailController::class, "send"])->name('emails.send');
    Route::get('emails/compose', [EmailController::class, "compose"])->name('emails.compose');
    Route::get('emails', [EmailController::class, "index"])->name('emails.index');
    Route::get('emails/{id}', [EmailController::class, "show"])->name('emails.show');
    Route::get('emails/users/all', [EmailController::class, "countAll"])->name('emails.users.all');
});
