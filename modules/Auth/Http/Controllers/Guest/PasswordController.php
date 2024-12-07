<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function showForgetForm()
    {
        // Return forget password view
        return view('auth.passwords.email');
    }

    public function submitForgetForm(Request $request)
    {
        // Add logic to handle forget password
        return response()->json(['message' => 'Password reset link sent successfully.']);
    }

    public function showResetForm($token)
    {
        // Return reset password view
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function submitResetForm(Request $request)
    {
        // Add logic to handle password reset
        return response()->json(['message' => 'Password reset successfully.']);
    }
}
