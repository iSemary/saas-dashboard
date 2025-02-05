<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Entities\FactorAuthenticateToken;
use Modules\Auth\Entities\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class Validate2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        // Determine if the request is an API request or web request
        $isApiRequest = $request->is('api/*');

        // Get the authenticated user
        $user = $isApiRequest ? auth()->guard('api')->user() : Auth::user();

        if ($isApiRequest) {
            $tokenId = $user->token()->id;
            // Check if the user has an existing 2FA token
            $existingToken = FactorAuthenticateToken::where('user_id', $user->id)
                ->where("token_id", $tokenId)
                ->first();
        } else {
            $tokenId = $user->getCurrentToken();

            $existingToken = FactorAuthenticateToken::where('user_id', $user->id)
                ->where("token_id", $tokenId)
                ->first();
        }

        if (!$existingToken) {
            if ($isApiRequest) {
                // For API requests, return a JSON response
                return response()->json(['error' => '2FA token is not valid.'], 409);
            } else {
                // For web requests, redirect to 2FA page
                $redirectUrl = $request->query('redirect');
                if (User::where("id", $user->id)->first()->google2fa_secret) {
                    return redirect()->route('2fa.validate', ['redirect' => $redirectUrl]);
                } else {
                    return redirect()->route('2fa.setup', ['redirect' => $redirectUrl]);
                }
            }
        }

        // lock screen checker
        if ($request->cookie('lock') == 1 && !in_array(Route::currentRouteName(), ['lock.show', 'unlock.submit', 'logout'])) {
            return redirect()->route('lock.show');
        }
        return $next($request);
    }
}
