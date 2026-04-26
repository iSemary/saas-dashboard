<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\DTOs\CreatePaymentMethodData;
use Modules\Payment\Http\Requests\StorePaymentMethodRequest;
use Modules\Payment\Services\PaymentMethodService;

class PaymentMethodApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected PaymentMethodService $service) {}

    public function index(Request $request)
    {
        $methods = \Modules\Utilities\Entities\Type::where('slug', 'payment-method')
            ->orWhereIn('name', ['Stripe', 'PayPal', 'Razorpay', 'Cash', 'Bank Transfer'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 50));
        return $this->apiPaginated($methods);
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $data = CreatePaymentMethodData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
