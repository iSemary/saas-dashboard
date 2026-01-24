<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Entities\User;
use Modules\Auth\Http\Controllers\Guest\AuthController as WebAuthController;
use Modules\Auth\Http\Controllers\Guest\TwoFactorAuthController as Web2FAController;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;
use PragmaRX\Google2FA\Google2FA;

class AuthApiController extends ApiController
{
    protected WebAuthController $webAuthController;
    protected Web2FAController $web2FAController;

    public function __construct()
    {
        $this->webAuthController = app(WebAuthController::class);
        $this->web2FAController = app(Web2FAController::class);
    }

    /**
     * Login user via API
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Get subdomain from host header
        $subdomain = TenantHelper::getSubDomain();
        if (!$subdomain || $subdomain === 'www' || $subdomain === '') {
            return response()->json([
                'message' => 'Invalid organization',
            ], 400);
        }

        $tenant = Tenant::where('domain', $subdomain)->first();
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found',
            ], 400);
        }

        TenantHelper::makeCurrent($tenant->name);

        // Determine if username is email or username
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login
        $credentials = [
            $field => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::guard('web')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::guard('web')->user();

        // Check if 2FA is enabled
        if ($user->google2fa_secret) {
            // Generate temporary token for 2FA verification
            $tempToken = $this->generateTempToken($user);
            
            Auth::guard('web')->logout();
            
            // Return format for 2FA required
            return response()->json([
                'requires_2fa' => true,
                'temp_token' => $tempToken,
                'message' => 'Please verify your 2FA code',
            ], 200);
        }

        // Generate access token
        $token = $user->createToken('web-app')->accessToken;

        // Get user data with permissions
        $userData = $this->formatUserData($user);

        // Return format that matches frontend expectations
        return response()->json([
            'token' => $token,
            'user' => $userData,
        ], 200);
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request): JsonResponse
    {
        $request->validate([
            'temp_token' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // Get user from temp token
        $user = $this->getUserFromTempToken($request->temp_token);
        if (!$user) {
            return response()->json([
                'message' => 'Invalid temporary token',
            ], 401);
        }

        // Get subdomain and set tenant
        $subdomain = TenantHelper::getSubDomain();
        $tenant = Tenant::where('domain', $subdomain)->first();
        if ($tenant) {
            TenantHelper::makeCurrent($tenant->name);
        }

        // Verify 2FA code
        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$isValid) {
            return response()->json([
                'message' => 'Invalid verification code',
            ], 422);
        }

        // Login user
        Auth::guard('web')->login($user);

        // Generate access token
        $token = $user->createToken('web-app')->accessToken;

        // Get user data
        $userData = $this->formatUserData($user);

        // Return format that matches frontend expectations
        return response()->json([
            'token' => $token,
            'user' => $userData,
        ], 200);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        // Get user from Passport token
        $user = $request->user('api');
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $userData = $this->formatUserData($user);

        return response()->json([
            'user' => $userData,
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('api');
        
        if ($user) {
            // Revoke the current token
            $request->user('api')->token()->revoke();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Setup 2FA
     */
    public function setup2FA(Request $request): JsonResponse
    {
        $user = $request->user('api');
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($user->google2fa_secret) {
            return response()->json([
                'message' => '2FA is already enabled',
            ], 400);
        }

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        
        $qrCodeUrl = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ], 200);
    }

    /**
     * Confirm 2FA setup
     */
    public function confirm2FA(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'secret' => 'required|string',
        ]);

        $user = $request->user('api');
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $google2fa = app('pragmarx.google2fa');
        $isValid = $google2fa->verifyKey($request->secret, $request->code);

        if (!$isValid) {
            return response()->json([
                'message' => 'Invalid verification code',
            ], 422);
        }

        // Save 2FA secret
        $user->update(['google2fa_secret' => $request->secret]);

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Store recovery codes (you may want to store these in a database)
        // For now, we'll return them to the user

        return response()->json([
            'message' => '2FA has been enabled',
            'recovery_codes' => $recoveryCodes,
        ], 200);
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request): JsonResponse
    {
        $user = $request->user('api');
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $user->update(['google2fa_secret' => null]);

        return response()->json([
            'message' => '2FA has been disabled',
        ], 200);
    }

    /**
     * Get recovery codes
     */
    public function getRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user('api');
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        // In a real implementation, you'd retrieve these from the database
        // For now, return empty array
        return response()->json([
            'recovery_codes' => [],
        ], 200);
    }

    /**
     * Format user data for API response
     */
    private function formatUserData(User $user): array
    {
        $user->load('roles.permissions');
        
        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->name;
            }
        }
        $permissions = array_unique($permissions);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'two_factor_enabled' => !empty($user->google2fa_secret),
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            })->toArray(),
            'permissions' => $permissions,
        ];
    }

    /**
     * Generate temporary token for 2FA verification
     */
    private function generateTempToken(User $user): string
    {
        // Store temp token in cache with user ID
        $tempToken = bin2hex(random_bytes(32));
        cache()->put("2fa_temp_token_{$tempToken}", $user->id, now()->addMinutes(10));
        return $tempToken;
    }

    /**
     * Get user from temporary token
     */
    private function getUserFromTempToken(string $tempToken): ?User
    {
        $userId = cache()->get("2fa_temp_token_{$tempToken}");
        if (!$userId) {
            return null;
        }

        // Get subdomain and set tenant context
        $subdomain = TenantHelper::getSubDomain();
        $tenant = Tenant::where('domain', $subdomain)->first();
        if ($tenant) {
            TenantHelper::makeCurrent($tenant->name);
        }

        return User::find($userId);
    }

    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Register new user (placeholder)
     */
    public function register(Request $request): JsonResponse
    {
        // Implement registration if needed
        return response()->json([
            'message' => 'Registration not implemented via API',
        ], 501);
    }
}
