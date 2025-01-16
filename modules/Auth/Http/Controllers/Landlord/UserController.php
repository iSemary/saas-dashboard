<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function __construct() {}

    public function profile() {
        $user = auth()->user();
        return view("landlord.auth.profile.index", compact('user'));
    }

    public function updateProfile(Request $request) {}
}
