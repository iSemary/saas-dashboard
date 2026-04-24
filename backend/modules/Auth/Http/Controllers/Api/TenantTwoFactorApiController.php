<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\Tenant\TenantTwoFactorApiService;

class TenantTwoFactorApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantTwoFactorApiService $twoFactorService) {}

    public function setup(Request $request)
    {
        $result = $this->twoFactorService->setup($request->user()->id);
        return $this->apiSuccess($result);
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate(['code' => 'required|string']);
        $result = $this->twoFactorService->confirm($request->user()->id, $validated['code']);

        if (isset($result['error'])) {
            return $this->apiError($result['error'], $result['code']);
        }

        return $this->apiSuccess(null, $result['message']);
    }

    public function disable(Request $request)
    {
        $this->twoFactorService->disable($request->user()->id);
        return $this->apiSuccess(null, '2FA disabled successfully');
    }
}
