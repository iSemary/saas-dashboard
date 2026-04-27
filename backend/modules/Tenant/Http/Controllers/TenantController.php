<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Tenant\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TenantController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(TenantService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.tenants', only: ['index', 'show']),
            new Middleware('permission:create.tenants', only: ['create', 'store']),
            new Middleware('permission:update.tenants', only: ['edit', 'update']),
            new Middleware('permission:delete.tenants', only: ['destroy']),
            new Middleware('permission:restore.tenants', only: ['restore']),
        ];
    }
    
    public function index()
    {
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
        return $this->return(200, translate("created_successfully"));
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

    /**
     * Re-migrate tenant database
     */
    public function reMigrate($id)
    {
        $result = $this->service->reMigrate($id);
        
        if ($result['success']) {
            return $this->return(200, $result['message']);
        } else {
            return $this->return(500, $result['message']);
        }
    }

    /**
     * Seed tenant database
     */
    public function seedDatabase($id)
    {
        $result = $this->service->seedDatabase($id);
        
        if ($result['success']) {
            return $this->return(200, $result['message']);
        } else {
            return $this->return(500, $result['message']);
        }
    }

    /**
     * Re-seed tenant database
     */
    public function reSeedDatabase($id)
    {
        $result = $this->service->reSeedDatabase($id);
        
        if ($result['success']) {
            return $this->return(200, $result['message']);
        } else {
            return $this->return(500, $result['message']);
        }
    }

    /**
     * Get tenant database health
     */
    public function getDatabaseHealth($id)
    {
        $health = $this->service->getDatabaseHealth($id);
        
        if ($health) {
            return response()->json($health);
        } else {
            return response()->json(['error' => 'Tenant not found'], 404);
        }
    }
}
