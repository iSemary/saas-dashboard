<?php

use Illuminate\Support\Facades\Route;
use Modules\Localization\Http\Controllers\LanguageController;
use Modules\Localization\Http\Controllers\TranslationController;

Route::middleware(['auth:web', '2fa'])->group(function () {
    Route::get('translations/objects/{objectId}', [TranslationController::class, 'getObjectTranslations'])->name('translations.object.show');
    Route::put('translations/objects/{objectId}', [TranslationController::class, 'updateObjectTranslations'])->name('translations.object.update');
});

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('languages', LanguageController::class)->names('languages');

    Route::get('translations/used-translations/js', [TranslationController::class, 'getUsedTranslationInJs'])->name('translations.used-translation-js');
    Route::get('translations/used-translations/php', [TranslationController::class, 'getUsedTranslationInPhp'])->name('translations.used-translation-php');
    Route::post('translations/generate-json', [TranslationController::class, 'generateJson'])->name('translations.generate-json');
    Route::post('translations/sync-missing', [TranslationController::class, 'syncMissing'])->name('translations.sync-missing');
    Route::resource('translations', TranslationController::class)->names('translations');
});
