<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\AuthService;

class TenantProfileApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected AuthService $authService) {}

    public function show(Request $request)
    {
        return $this->apiSuccess($this->authService->formatUserData($request->user()));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|max:255|unique:users,email,{$user->id}",
        ]);
        $freshUser = $this->authService->updateProfile($user, $validated);
        return $this->apiSuccess($freshUser, 'Profile updated successfully');
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate(['avatar' => 'required|image|max:2048']);
        $result = $this->authService->uploadAvatar($request->user(), $request->file('avatar'));
        return $this->apiSuccess($result, 'Avatar uploaded successfully');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        $result = $this->authService->changePassword($request->user(), $validated['current_password'], $validated['new_password']);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess(null, $result['message']);
    }
}
