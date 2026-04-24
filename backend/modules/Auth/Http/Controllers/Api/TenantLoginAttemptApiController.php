<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\Tenant\TenantLoginAttemptApiService;

class TenantLoginAttemptApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantLoginAttemptApiService $loginAttemptService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->loginAttemptService->list($filters, $request->get('per_page', 50)));
    }
}
