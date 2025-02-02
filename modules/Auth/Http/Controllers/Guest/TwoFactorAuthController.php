<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Auth\Entities\FactorAuthenticateToken;
use Modules\Auth\Entities\User;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class TwoFactorAuthController extends ApiController
{
    public function showSetupForm()
    {
        $user = auth()->user();
        if (!$user->google2fa_secret) {
            $generator = $this->generate2FACode($user);
            return view('guest.auth.2fa.setup', ['qrCode' => $generator['qr_code'], 'secretKey' => $generator['secret_key']]);
        }
        return redirect()->route("2fa.validate");
    }

    public function showValidateForm()
    {
        $user = auth()->user();
        if ($user->google2fa_secret) {
            return view('guest.auth.2fa.validate');
        }
        return redirect()->route("2fa.setup");
    }

    /**
     * Generate 2FA QrCode After registration
     *
     * @return void
     */
    private function generate2FACode(User $user)
    {
        $google2fa = app('pragmarx.google2fa');
        $googleSecretKey = $google2fa->generateSecretKey();

        $qrCode = $google2fa->getQRCodeInline(config('app.name'), $user['email'], $googleSecretKey);
        return ['qr_code' => $qrCode, 'secret_key' => $googleSecretKey];
    }

    /**
     * Verify 2FA After registration
     *
     * @param Request $request
     * @return void
     */
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string|max:6', 'secret_key' => 'required|string']);

        // Verify the 2FA code
        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($request->secret_key, $request->otp);

        if ($isValid) {
            $user = auth()->user();
            // Create a new 2FA token
            $tokenId = $user->getCurrentToken();

            FactorAuthenticateToken::create(['user_id' => $user->id, 'token_id' => $tokenId]);
            $user->update(["google2fa_secret" => $request->secret_key]);
            return $this->return(200, "2FA Verified Successfully", ['redirect' => $this->handleRedirection($request)]);
        }
        return $this->return(400, "Invalid OTP number");
    }

    public function check(Request $request)
    {
        $user = auth()->user();
        $request->validate(['otp' => 'required|string|max:6']);

        // Validate the 2FA code
        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if ($isValid) {
            $tokenId = $user->getCurrentToken();
            // Create a new 2FA token
            FactorAuthenticateToken::create(['user_id' => $user->id, 'token_id' => $tokenId]);
            return $this->return(200, "2FA Verified Successfully", ['redirect' => $this->handleRedirection($request)]);
        }
        return $this->return(400, "Invalid OTP number");
    }

    private function handleRedirection(Request $request)
    {
        $subDomain = TenantHelper::getSubDomain();
        if ($subDomain) {
            $tenant = Tenant::where('domain', $subDomain)->first();
            if ($tenant) {
                return TenantHelper::generateURL($tenant->name) . ($request->redirect ? "?redirect=" . $request->redirect : "");
            }
        }
        return '/';
    }
}
