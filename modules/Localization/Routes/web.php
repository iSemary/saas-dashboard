<?php

use Illuminate\Support\Facades\Route;

use Modules\Localization\Http\Controllers\LanguageController;
use Modules\Localization\Http\Controllers\TranslationController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('languages', LanguageController::class)->names('languages');

    Route::post('translations/generate-json', [TranslationController::class, 'generateJson'])->name('translations.generate-json');
    Route::post('translations/sync-missing', [TranslationController::class, 'syncMissing'])->name('translations.sync-missing');
    Route::resource('translations', TranslationController::class)->names('translations');
});
