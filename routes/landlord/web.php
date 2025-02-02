<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {});
