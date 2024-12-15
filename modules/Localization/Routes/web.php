<?php

use Illuminate\Support\Facades\Route;

use Modules\Localization\Http\Controllers\LanguageController;
use Modules\Localization\Http\Controllers\TranslationController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('languages', LanguageController::class)->names('languages');
    Route::resource('translations', TranslationController::class)->names('translations');
});
