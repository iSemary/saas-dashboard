<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Return login view
        return view('guest.auth.login');
    }

    public function login(Request $request)
    {
        // Add logic for user login
        return response()->json(['message' => 'Login successful.']);
    }

    public function showRegistrationForm()
    {
        // Return registration view
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Add logic for user registration
        return response()->json(['message' => 'Registration successful.']);
    }
}
