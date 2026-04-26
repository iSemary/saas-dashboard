<?php

namespace Modules\Tenant\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Tenant\Services\TenantModuleService;

class TenantModuleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantModuleService $moduleService)
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

    /**
     * Get the current tenant's subscribed modules with summary data.
     */
    public function index()
    {
        $data = $this->moduleService->getSubscribedModules();
        return $this->apiSuccess($data);
    }

    /**
     * Get a single subscribed module by key.
     */
    public function show(string $moduleKey)
    {
        $data = $this->moduleService->getSubscribedModule($moduleKey);

        if (!$data) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }

        return $this->apiSuccess($data);
    }
}
