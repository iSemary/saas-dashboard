<?php

namespace Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Sales\Services\PosDashboardService;

class PosApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected PosDashboardService $dashboardService) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        return $this->apiSuccess($data);
    }
}
