<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PaymentDashboardController extends Controller
{
    /**
     * Display the payment dashboard
     */
    public function index()
    {
        return view('payment::dashboard.index');
    }
}
