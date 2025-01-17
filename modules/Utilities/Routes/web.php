<?php

use Illuminate\Support\Facades\Route;
use Modules\Utilities\Http\Controllers\CurrencyController;
use Modules\Utilities\Http\Controllers\CategoryController;
use Modules\Utilities\Http\Controllers\CodeBuilderController;
use Modules\Utilities\Http\Controllers\AnalysisController;
use Modules\Utilities\Http\Controllers\AnnouncementController;
use Modules\Utilities\Http\Controllers\IndustryController;
use Modules\Utilities\Http\Controllers\ModuleController;
use Modules\Utilities\Http\Controllers\ReleaseController;
use Modules\Utilities\Http\Controllers\TagController;
use Modules\Utilities\Http\Controllers\TypeController;
use Modules\Utilities\Http\Controllers\UnitController;


Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('categories', CategoryController::class)->names('categories');

    Route::resource('currencies', CurrencyController::class)->names('currencies');

    // Tags and Tag Values
    Route::resource('tags', TagController::class)->names('tags');

    Route::resource('modules', ModuleController::class)->names('modules');
    Route::resource('announcements', AnnouncementController::class)->names('announcements');
    Route::resource('releases', ReleaseController::class)->names('releases');
    Route::resource('types', TypeController::class)->names('types');
    Route::resource('industries', IndustryController::class)->names('industries');
    Route::resource('units', UnitController::class)->names('units');

    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        Route::get("code-builder", [CodeBuilderController::class, "show"])->name("code-builder.show");
        Route::post("code-builder", [CodeBuilderController::class, "submit"])->name("code-builder.submit");
        Route::get("env-diff", [AnalysisController::class, "showEnvDiff"])->name("env-diff.show");
    });
});
