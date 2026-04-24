<?php

namespace Modules\CRM\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\CRM\Services\CrmDashboardService;

class CrmApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected CrmDashboardService $dashboardService) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        return $this->apiSuccess($data);
    }
}
