<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;
use Modules\Development\Services\SystemStatusService;

class SystemStatusApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected SystemStatusService $service) {}

    public function index()
    {
        return $this->apiSuccess($this->service->getStatus());
    }
}
