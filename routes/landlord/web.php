<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::get('test', function () {
        $pdf = Pdf::loadView('guest.auth.login');
        return $pdf->download(('invoice.pdf'));
    });
});
