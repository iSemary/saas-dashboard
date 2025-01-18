<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\RoleService;
use Illuminate\Http\Request;
use Modules\Auth\Services\PermissionService;

class RoleController extends ApiController
{
    protected $service;
    protected $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->service = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate("roles");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => "roles"],
        ];

        $actionButtons = [
            [
                'text' => translate('create') . " " . translate("role"),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.roles.create'),
                    'data-modal-title' => translate('create') . " " . translate("role"),
                ]
            ],
        ];

        return view('landlord.auth.roles.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $permissions = $this->permissionService->getAll();
        return view('landlord.auth.roles.editor', compact('permissions'));
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
        $permissions = $this->permissionService->getAll();
        $row = $this->service->get($id);
        return view('landlord.auth.roles.editor', compact('row', 'permissions'));
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
