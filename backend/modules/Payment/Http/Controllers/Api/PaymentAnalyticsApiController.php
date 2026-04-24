<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentAnalyticsApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request)
    {
        $data = [
            'total_revenue' => 0,
            'revenue_by_method' => [],
            'revenue_by_status' => [],
            'monthly_revenue' => [],
        ];
        return $this->apiSuccess($data);
    }
}
