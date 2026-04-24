<?php

namespace Modules\HR\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\HR\Services\HrDashboardService;

class HrApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected HrDashboardService $dashboardService) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        return $this->apiSuccess($data);
    }
}
