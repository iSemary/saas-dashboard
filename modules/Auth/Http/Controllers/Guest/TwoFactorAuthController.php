<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    public function showForm()
    {
        // Return 2FA form view
        return view('auth.two_factor');
    }

    public function verify(Request $request)
    {
        // Add logic to verify 2FA
        return response()->json(['message' => 'Two-factor authentication verified successfully.']);
    }
}
