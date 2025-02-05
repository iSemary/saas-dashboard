<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Cookie;
use DB;
use Exception;
use Illuminate\Http\Request;
use Modules\Auth\Entities\User;
use Modules\Auth\Http\Requests\ForgetPasswordRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Jobs\ForgetPasswordMailJob;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class PasswordController extends ApiController
{
    public function showForgetForm()
    {
        $subdomain = TenantHelper::getSubDomain();
        if ($subdomain) {
            $tenant = Tenant::where("name", $subdomain)->exists();
            if ($tenant) {
                return view('guest.auth.password.forget', ['tenant' => $subdomain]);
            } else {
                throw new Exception("Invalid organization.");
            }
        } else {
            return view('guest.auth.password.forget');
        }
    }

    public function submitForgetForm(ForgetPasswordRequest $forgetPasswordRequest)
    {
        $tenant = TenantHelper::makeCurrent($forgetPasswordRequest->subdomain);

        $user = User::where('email', $forgetPasswordRequest->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Save token in password resets table
        $token = $user->createResetToken();

        // Dispatch job to send reset link email
        ForgetPasswordMailJob::dispatch($user, $token, $tenant->id);

        return response()->json(['message' => 'Password reset link sent successfully.']);
    }

    public function showResetForm(string $token, Request $request)
    {
        if (!TenantHelper::getSubDomain()) {
            throw new Exception('Organization domain is required.');
        }
        // Check if token exists and is valid
        $resetToken = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$resetToken) {
            // If token is not found, redirect or return an error message
            throw new Exception('Invalid or expired token.');
        }

        // Return reset password view
        return view('guest.auth.password.reset', ['token' => $token]);
    }

    public function submitResetForm(ResetPasswordRequest $resetPasswordRequest)
    {
        // Fetch the user by the token from the database
        $user = User::join("password_reset_tokens", "password_reset_tokens.user_id", "users.id")
            ->select(['users.id'])
            ->where("password_reset_tokens.token", $resetPasswordRequest->token)
            ->first();
        if ($user) {
            // Reset the user's password
            $user->updatePassword($resetPasswordRequest->password);
            // clear reset token from database
            DB::table('password_reset_tokens')->where("token", $resetPasswordRequest->token)->delete();
            return $this->return(200, "Password has been reset successfully", ['redirect' => route("login")]);
        } else {
            return $this->return(400, "User not exists");
        }
    }

    public function showLock()
    {
        if (request()->cookie('lock') == 1) {
            return view("guest.auth.lock.index");
        }
        return redirect()->route('home');
    }

    public function lock()
    {
        Cookie::queue(Cookie::forever('lock', 1));

        return redirect()->route('lock.show');
    }

    // validate the user
    public function unlock(Request $request)
    {
        if (!auth()->validate([
            'email' => auth()->user()->email,
            'password' => $request->password
        ])) {
            return $this->return(422, translate('incorrect_password'));
        }

        Cookie::queue(Cookie::forget('lock'));
        return $this->return(200, 'Unlocked successfully', [
            'redirect' => route('home')
        ]);
    }
}
