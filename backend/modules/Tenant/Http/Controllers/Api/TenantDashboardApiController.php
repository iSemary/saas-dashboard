<?php

namespace Modules\Tenant\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Tenant\Services\TenantDashboardService;

class TenantDashboardApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantDashboardService $dashboardService) {}

    public function stats()
    {
        $data = $this->dashboardService->getStats();
        return $this->apiSuccess($data);
    }
}
