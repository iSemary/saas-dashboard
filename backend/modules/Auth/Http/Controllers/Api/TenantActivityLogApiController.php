<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\Tenant\TenantActivityLogApiService;

class TenantActivityLogApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantActivityLogApiService $activityLogService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->activityLogService->list($filters, $request->get('per_page', 50)));
    }
}
