<?php

use Illuminate\Support\Facades\Route;

Route::get('/landlord', function () {
    return view('landlord.dashboard');
});
