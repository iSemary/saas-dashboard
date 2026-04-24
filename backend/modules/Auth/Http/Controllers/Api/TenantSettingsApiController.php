<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $this->settingsService->update($validated['settings']);
        return $this->apiSuccess(null, 'Settings updated successfully');
    }
}
