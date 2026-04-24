<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Development\Services\SystemHealthService;

class SystemHealthApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected SystemHealthService $service) {}

    public function index()
    {
        return $this->apiSuccess($this->service->getHealthChecks());
    }
}
