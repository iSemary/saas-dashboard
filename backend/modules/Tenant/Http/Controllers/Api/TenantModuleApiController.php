<?php

namespace Modules\Tenant\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Tenant\Services\TenantModuleService;

class TenantModuleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantModuleService $moduleService) {}

    /**
     * Get the current tenant's subscribed modules with summary data.
     */
    public function index()
    {
        $data = $this->moduleService->getSubscribedModules();
        return $this->apiSuccess($data);
    }
}
