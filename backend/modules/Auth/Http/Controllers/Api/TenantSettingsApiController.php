<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\UpdateTenantSettingsRequest;
use Modules\Auth\Services\Tenant\TenantSettingsService;

class TenantSettingsApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantSettingsService $settingsService) {}

    public function index()
    {
        $settings = $this->settingsService->all();
        return $this->apiSuccess($settings);
    }

    public function update(UpdateTenantSettingsRequest $request)
    {
        $this->settingsService->update($request->settings);
        return $this->apiSuccess(null, translate('message.updated_successfully'));
    }
}
