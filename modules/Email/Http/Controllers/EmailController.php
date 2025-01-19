<?php

namespace Modules\Email\Http\Controllers;

use App\Http\Controllers\ApiController;

class EmailController extends ApiController
{
    public function index()
    {

    }
    
    public function compose()
    {
        return view("landlord.emails.compose");
    }

    public function resend()
    {

    }
}
