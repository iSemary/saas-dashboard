<?php

use Illuminate\Support\Facades\Route;
use Modules\Survey\Presentation\Http\Controllers\Api;

// ==========================================
// Authenticated Routes (Tenant API)
// ==========================================
Route::middleware(['auth:api', 'tenant'])
    ->prefix('tenant/survey')
    ->name('tenant.survey.')
    ->group(function () {

        // Survey Management
        Route::get('surveys', [Api\SurveyController::class, 'index'])->name('surveys.index');
        Route::post('surveys', [Api\SurveyController::class, 'store'])->name('surveys.store');
        Route::get('surveys/{id}', [Api\SurveyController::class, 'show'])->name('surveys.show');
        Route::put('surveys/{id}', [Api\SurveyController::class, 'update'])->name('surveys.update');
        Route::delete('surveys/{id}', [Api\SurveyController::class, 'destroy'])->name('surveys.destroy');
        Route::post('surveys/{id}/duplicate', [Api\SurveyController::class, 'duplicate'])->name('surveys.duplicate');
        Route::post('surveys/{id}/publish', [Api\SurveyController::class, 'publish'])->name('surveys.publish');
        Route::post('surveys/{id}/close', [Api\SurveyController::class, 'close'])->name('surveys.close');
        Route::post('surveys/{id}/pause', [Api\SurveyController::class, 'pause'])->name('surveys.pause');
        Route::post('surveys/{id}/resume', [Api\SurveyController::class, 'resume'])->name('surveys.resume');

        // Survey Pages
        Route::get('surveys/{surveyId}/pages', [Api\SurveyPageController::class, 'index'])->name('pages.index');
        Route::post('surveys/{surveyId}/pages', [Api\SurveyPageController::class, 'store'])->name('pages.store');
        Route::put('pages/{id}', [Api\SurveyPageController::class, 'update'])->name('pages.update');
        Route::delete('pages/{id}', [Api\SurveyPageController::class, 'destroy'])->name('pages.destroy');
        Route::post('surveys/{surveyId}/pages/reorder', [Api\SurveyPageController::class, 'reorder'])->name('pages.reorder');

        // Survey Questions
        Route::get('surveys/{surveyId}/questions', [Api\SurveyQuestionController::class, 'index'])->name('questions.index');
        Route::post('surveys/{surveyId}/questions', [Api\SurveyQuestionController::class, 'store'])->name('questions.store');
        Route::get('questions/{id}', [Api\SurveyQuestionController::class, 'show'])->name('questions.show');
        Route::put('questions/{id}', [Api\SurveyQuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{id}', [Api\SurveyQuestionController::class, 'destroy'])->name('questions.destroy');
        Route::post('surveys/{surveyId}/questions/reorder', [Api\SurveyQuestionController::class, 'reorder'])->name('questions.reorder');

        // Question Options
        Route::get('questions/{questionId}/options', [Api\SurveyQuestionOptionController::class, 'index'])->name('options.index');
        Route::post('questions/{questionId}/options', [Api\SurveyQuestionOptionController::class, 'store'])->name('options.store');
        Route::put('options/{id}', [Api\SurveyQuestionOptionController::class, 'update'])->name('options.update');
        Route::delete('options/{id}', [Api\SurveyQuestionOptionController::class, 'destroy'])->name('options.destroy');
        Route::post('questions/{questionId}/options/reorder', [Api\SurveyQuestionOptionController::class, 'reorder'])->name('options.reorder');

        // Survey Responses
        Route::get('surveys/{surveyId}/responses', [Api\SurveyResponseController::class, 'index'])->name('responses.index');
        Route::get('responses/{id}', [Api\SurveyResponseController::class, 'show'])->name('responses.show');
        Route::delete('responses/{id}', [Api\SurveyResponseController::class, 'destroy'])->name('responses.destroy');
        Route::get('surveys/{surveyId}/analytics', [Api\SurveyResponseController::class, 'analytics'])->name('analytics');

        // Templates
        Route::get('templates', [Api\SurveyTemplateController::class, 'index'])->name('templates.index');
        Route::get('templates/{id}', [Api\SurveyTemplateController::class, 'show'])->name('templates.show');
        Route::post('templates/{id}/create-survey', [Api\SurveyTemplateController::class, 'createSurvey'])->name('templates.create-survey');

        // Themes
        Route::get('themes', [Api\SurveyThemeController::class, 'index'])->name('themes.index');
        Route::post('themes', [Api\SurveyThemeController::class, 'store'])->name('themes.store');
        Route::get('themes/{id}', [Api\SurveyThemeController::class, 'show'])->name('themes.show');
        Route::put('themes/{id}', [Api\SurveyThemeController::class, 'update'])->name('themes.update');
        Route::delete('themes/{id}', [Api\SurveyThemeController::class, 'destroy'])->name('themes.destroy');

        // Shares
        Route::get('surveys/{surveyId}/shares', [Api\SurveyShareController::class, 'index'])->name('shares.index');
        Route::post('surveys/{surveyId}/shares', [Api\SurveyShareController::class, 'store'])->name('shares.store');
        Route::delete('shares/{id}', [Api\SurveyShareController::class, 'destroy'])->name('shares.destroy');

        // Automation Rules
        Route::get('surveys/{surveyId}/automation-rules', [Api\SurveyAutomationRuleController::class, 'index'])->name('automation-rules.index');
        Route::post('surveys/{surveyId}/automation-rules', [Api\SurveyAutomationRuleController::class, 'store'])->name('automation-rules.store');
        Route::get('automation-rules/{id}', [Api\SurveyAutomationRuleController::class, 'show'])->name('automation-rules.show');
        Route::put('automation-rules/{id}', [Api\SurveyAutomationRuleController::class, 'update'])->name('automation-rules.update');
        Route::delete('automation-rules/{id}', [Api\SurveyAutomationRuleController::class, 'destroy'])->name('automation-rules.destroy');
        Route::post('automation-rules/{id}/toggle', [Api\SurveyAutomationRuleController::class, 'toggle'])->name('automation-rules.toggle');

        // Webhooks
        Route::get('surveys/{surveyId}/webhooks', [Api\SurveyWebhookController::class, 'index'])->name('webhooks.index');
        Route::post('surveys/{surveyId}/webhooks', [Api\SurveyWebhookController::class, 'store'])->name('webhooks.store');
        Route::get('webhooks/{id}', [Api\SurveyWebhookController::class, 'show'])->name('webhooks.show');
        Route::put('webhooks/{id}', [Api\SurveyWebhookController::class, 'update'])->name('webhooks.update');
        Route::delete('webhooks/{id}', [Api\SurveyWebhookController::class, 'destroy'])->name('webhooks.destroy');
        Route::post('webhooks/{id}/toggle', [Api\SurveyWebhookController::class, 'toggle'])->name('webhooks.toggle');
        Route::post('webhooks/{id}/regenerate-secret', [Api\SurveyWebhookController::class, 'regenerateSecret'])->name('webhooks.regenerate-secret');

        // Dashboard
        Route::get('dashboard', [Api\SurveyDashboardController::class, 'index'])->name('dashboard');
    });

// ==========================================
// Public Routes (No Auth - For Respondents)
// ==========================================
Route::prefix('public/survey/{token}')
    ->name('public.survey.')
    ->group(function () {
        Route::get('/', [Api\PublicSurveyController::class, 'show'])->name('show');
        Route::post('/start', [Api\PublicSurveyController::class, 'start'])->name('start');
        Route::post('/answer', [Api\PublicSurveyController::class, 'answer'])->name('answer');
        Route::post('/complete', [Api\PublicSurveyController::class, 'complete'])->name('complete');
        Route::get('/resume/{resumeToken}', [Api\PublicSurveyController::class, 'resume'])->name('resume');
    });

// ==========================================
// Embed Route
// ==========================================
Route::get('embed/survey/{token}', [Api\EmbedSurveyController::class, 'show'])
    ->name('embed.survey.show');
