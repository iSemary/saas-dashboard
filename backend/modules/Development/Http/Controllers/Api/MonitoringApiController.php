<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Development\Services\MonitoringService;

class MonitoringApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected MonitoringService $service) {}

    public function index()
    {
        return $this->apiSuccess($this->service->getDashboardData());
    }
}
