<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\ChangePasswordRequest;
use Modules\Auth\Http\Requests\Confirm2FARequest;
use Modules\Auth\Http\Requests\CreateApiKeyRequest;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\GlobalSearchRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Http\Requests\UpdateProfileRequest;
use Modules\Auth\Http\Requests\UploadAvatarRequest;
use Modules\Auth\Http\Requests\Verify2FARequest;
use Modules\Auth\Services\AuthService;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class AuthApiController extends ApiController
{
    use ApiResponseEnvelope;

    public function __construct(protected AuthService $authService) {}

    /**
     * Login user via API
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $subdomain = $request->input('subdomain') ?: TenantHelper::getSubDomain();
        $result = $this->authService->login($request->email, $request->password, $subdomain);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        if (isset($result['requires_2fa'])) {
            return $this->apiSuccess([
                'requires_2fa' => true,
                'temp_token' => $result['temp_token'],
            ], 'Please verify your 2FA code');
        }

        return $this->apiSuccess([
            'token' => $result['token'],
            'user' => $result['user'],
        ], 'Logged in successfully');
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Verify2FARequest $request): JsonResponse
    {
        $subdomain = $request->input('subdomain');
        $result = $this->authService->verify2FA($request->temp_token, $request->code, $subdomain);

        if (isset($result['error'])) {
            if ($result['code'] === 401) {
                return response()->json(['message' => $result['error']], 401);
            }
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess([
            'token' => $result['token'],
            'user' => $result['user'],
        ], '2FA verified successfully');
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if (!$user) {
            return $this->apiError('Unauthenticated', 401);
        }

        $userData = $this->authService->formatUserData($user);

        return $this->apiSuccess([
            'user' => $userData,
        ], 'User retrieved');
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if ($user) {
            $request->user('api')->token()->revoke();
        }

        return $this->apiSuccess(null, 'Logged out successfully');
    }

    /**
     * Setup 2FA
     */
    public function setup2FA(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if (!$user) {
            return $this->apiError('Unauthenticated', 401);
        }

        $result = $this->authService->setup2FA($user);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess($result, '2FA setup initiated');
    }

    /**
     * Confirm 2FA setup
     */
    public function confirm2FA(Confirm2FARequest $request): JsonResponse
    {
        $user = $request->user('api');

        if (!$user) {
            return $this->apiError('Unauthenticated', 401);
        }

        $result = $this->authService->confirm2FA($user, $request->code, $request->secret);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess($result, '2FA has been enabled');
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if (!$user) {
            return $this->apiError('Unauthenticated', 401);
        }

        $this->authService->disable2FA($user);

        return $this->apiSuccess(null, '2FA has been disabled');
    }

    /**
     * Get recovery codes
     */
    public function getRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user('api');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json(['recovery_codes' => []], 200);
    }

    /**
     * Register new user (placeholder)
     */
    public function register(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Registration not implemented via API',
        ], 501);
    }

    /**
     * Send password reset link via API
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $subdomain = $request->input('subdomain') ?: TenantHelper::getSubDomain();
        $result = $this->authService->forgotPassword($request->email, $subdomain);

        return response()->json(['message' => $result['message']], 200);
    }

    /**
     * Reset password via API
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $subdomain = $request->input('subdomain');
        $result = $this->authService->resetPassword($request->token, $request->password, $subdomain);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['message' => $result['message']], 200);
    }

    /**
     * Get user profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $userData = $this->authService->formatUserData($user);
        return response()->json(['data' => $userData]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $freshUser = $this->authService->updateProfile($user, $request->validated());

        return response()->json([
            'data' => $this->authService->formatUserData($freshUser),
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $result = $this->authService->uploadAvatar($user, $request->file('avatar'));

        return response()->json([
            'data' => $result,
            'message' => 'Avatar uploaded successfully'
        ]);
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $this->authService->removeAvatar($user);

        return response()->json(['message' => 'Avatar removed successfully']);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $result = $this->authService->changePassword($user, $request->current_password, $request->new_password);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }

    /**
     * Get active sessions
     */
    public function getSessions(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $sessions = $this->authService->getSessions($user);

        return response()->json(['data' => $sessions]);
    }

    /**
     * Revoke session
     */
    public function revokeSession(Request $request, $id): JsonResponse
    {
        $user = $request->user('api');
        $result = $this->authService->revokeSession($user, $id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }

    /**
     * Get API keys
     */
    public function getApiKeys(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $apiKeys = $this->authService->getApiKeys($user);

        return response()->json(['data' => $apiKeys]);
    }

    /**
     * Create API key
     */
    public function createApiKey(CreateApiKeyRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $result = $this->authService->createApiKey($user, $request->name, $request->scopes ?? ['*']);

        return response()->json([
            'data' => $result,
            'message' => 'API key created successfully. Please save the token as it will not be shown again.'
        ], 201);
    }

    /**
     * Revoke API key
     */
    public function revokeApiKey(Request $request, $id): JsonResponse
    {
        $user = $request->user('api');
        $result = $this->authService->revokeApiKey($user, $id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }

    /**
     * Global search
     */
    public function globalSearch(GlobalSearchRequest $request): JsonResponse
    {
        $results = $this->authService->globalSearch($request->q, $request->get('types', []));

        return response()->json(['data' => $results]);
    }
}
