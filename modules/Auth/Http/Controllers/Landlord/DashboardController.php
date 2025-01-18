<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;

class DashboardController extends ApiController
{
    public function index()
    {
        return view('landlord.dashboard.index');
    }
}
