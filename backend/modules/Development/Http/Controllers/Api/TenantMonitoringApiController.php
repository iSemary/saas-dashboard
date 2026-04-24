<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Development\Services\TenantMonitoringService;

class TenantMonitoringApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantMonitoringService $service) {}

    public function index()
    {
        return $this->apiSuccess($this->service->getTenantStatus());
    }
}
