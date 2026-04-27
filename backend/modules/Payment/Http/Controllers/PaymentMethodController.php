<?php

namespace Modules\Payment\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Payment\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PaymentMethodController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(PaymentMethodService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $title = translate($this->service->model->pluralTitle);
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.payment-methods.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.payment.payment-methods.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        $authenticationTypes = EnumHelper::getEnumFromTable($this->service->model->getTable(), "authentication_type");
        $processorTypes = ['stripe', 'paypal', 'razorpay', 'adyen', 'square'];
        $currencies = \Modules\Utilities\Entities\Currency::where('status', 'active')->get();
        
        return view('landlord.payment.payment-methods.editor', compact('statusOptions', 'authenticationTypes', 'processorTypes', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, translate("created_successfully"));
    }

    public function show($id) {}

    public function edit($id)
    {
        $row = $this->service->get($id);
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        $authenticationTypes = EnumHelper::getEnumFromTable($this->service->model->getTable(), "authentication_type");
        $processorTypes = ['stripe', 'paypal', 'razorpay', 'adyen', 'square'];
        $currencies = \Modules\Utilities\Entities\Currency::where('status', 'active')->get();
        
        return view('landlord.payment.payment-methods.editor', compact('row', 'statusOptions', 'authenticationTypes', 'processorTypes', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, translate("updated_successfully"));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }

    public function restore($id)
    {
        $this->service->restore($id);
        return $this->return(200, "Restored successfully");
    }

    public function test($id)
    {
        // TODO: Implement test method
        return $this->return(200, "Test completed successfully");
    }

    public function getGatewayConfig(Request $request)
    {
        // TODO: Implement getGatewayConfig method
        return $this->return(200, "Configuration retrieved", ['config' => []]);
    }

    public function export(Request $request)
    {
        // TODO: Implement export method
        return $this->return(200, "Export completed");
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.payment_methods', only: ['index', 'show']),
            new Middleware('permission:create.payment_methods', only: ['create', 'store']),
            new Middleware('permission:update.payment_methods', only: ['edit', 'update']),
            new Middleware('permission:delete.payment_methods', only: ['destroy']),
            new Middleware('permission:restore.payment_methods', only: ['restore']),
        ];
    }
}