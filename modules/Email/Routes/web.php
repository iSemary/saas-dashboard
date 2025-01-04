<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\EmailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('email-templates', EmailController::class)->names('email-templates');
    Route::get('email-logs', [EmailController::class, "index"])->name('email-logs.index');
});
