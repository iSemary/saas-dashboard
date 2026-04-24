<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected SubscriptionService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Subscription deleted successfully');
    }
}
