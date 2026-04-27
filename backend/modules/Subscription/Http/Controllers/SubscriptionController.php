<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Subscription\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SubscriptionController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }
    
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.subscriptions', only: ['index', 'show']),
            new Middleware('permission:create.subscriptions', only: ['create', 'store']),
            new Middleware('permission:update.subscriptions', only: ['edit', 'update']),
            new Middleware('permission:delete.subscriptions', only: ['destroy']),
            new Middleware('permission:restore.subscriptions', only: ['restore']),
        ];
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
                    'data-modal-link' => route('landlord.subscriptions.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.subscriptions.subscriptions.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = ['trial', 'active', 'past_due', 'canceled', 'expired', 'suspended'];
        $autoRenewOptions = ['enabled', 'disabled', 'pending_cancellation'];
        $billingCycleOptions = ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'];
        $plans = \Modules\Subscription\Entities\Plan::where('status', 'active')->get();
        $currencies = \Modules\Utilities\Entities\Currency::where('status', 'active')->get();
        $users = \Modules\Auth\Entities\User::select('id', 'name', 'email')->get();
        $brands = \Modules\Customer\Entities\Brand::select('id', 'name', 'tenant_id')->get();
        
        return view('landlord.subscriptions.subscriptions.editor', compact(
            'statusOptions', 'autoRenewOptions', 'billingCycleOptions', 
            'plans', 'currencies', 'users', 'brands'
        ));
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
        $statusOptions = ['trial', 'active', 'past_due', 'canceled', 'expired', 'suspended'];
        $autoRenewOptions = ['enabled', 'disabled', 'pending_cancellation'];
        $billingCycleOptions = ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'];
        $plans = \Modules\Subscription\Entities\Plan::where('status', 'active')->get();
        $currencies = \Modules\Utilities\Entities\Currency::where('status', 'active')->get();
        $users = \Modules\Auth\Entities\User::select('id', 'name', 'email')->get();
        
        return view('landlord.subscriptions.subscriptions.editor', compact(
            'row', 'statusOptions', 'autoRenewOptions', 'billingCycleOptions', 
            'plans', 'currencies', 'users'
        ));
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
        return $this->return(200, "Deleted successfully");
    }
}
