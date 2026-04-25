<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\PosDashboardService;

class PosDashboardApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly PosDashboardService $service) {}

    public function index(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->getDashboardData());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve dashboard data', 500, $e->getMessage());
        }
    }
}
