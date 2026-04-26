<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\ChangePasswordRequest;
use Modules\Auth\Http\Requests\UpdateTenantProfileRequest;
use Modules\Auth\Http\Requests\UploadAvatarRequest;
use Modules\Auth\Services\AuthService;

class TenantProfileApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected AuthService $authService) {}

    public function show(Request $request)
    {
        return $this->apiSuccess($this->authService->formatUserData($request->user()));
    }

    public function update(UpdateTenantProfileRequest $request)
    {
        $user = $request->user();
        $freshUser = $this->authService->updateProfile($user, $request->validated());
        return $this->apiSuccess($freshUser, translate('message.updated_successfully'));
    }

    public function uploadAvatar(UploadAvatarRequest $request)
    {
        $result = $this->authService->uploadAvatar($request->user(), $request->file('avatar'));
        return $this->apiSuccess($result, translate('message.action_completed'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->authService->changePassword($request->user(), $request->current_password, $request->new_password);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess(null, $result['message']);
    }
}
