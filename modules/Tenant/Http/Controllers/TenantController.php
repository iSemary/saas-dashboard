<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Tenant\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends ApiController
{
    protected $service;

    public function __construct(TenantService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate("tenants");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("tenants")],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " ". translate("tenant"),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.tenants.create'),
                    'data-modal-title' => translate("create") . " ". translate("tenant"),
                ]
            ],
        ];

        return view('landlord.tenant.tenants.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.tenant.tenants.editor');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $customerUsername = $data['customer_username'];

        $this->service->init($customerUsername);
        return $this->return(200, "Created successfully");
    }

    public function show($id) {}

    public function edit($id)
    {
        $row = $this->service->get($id);
        return view('landlord.tenant.tenants.editor', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, "Updated successfully");
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }
}
