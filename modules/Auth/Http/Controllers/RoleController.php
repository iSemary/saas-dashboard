<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\RoleService;
use Illuminate\Http\Request;
use Modules\Auth\Services\PermissionService;
use Modules\Auth\Services\PermissionGroupService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RoleController extends ApiController implements HasMiddleware
{
    protected $service;
    protected $permissionService;
    protected $permissionGroupService;

    public function __construct(RoleService $roleService, PermissionService $permissionService, PermissionGroupService $permissionGroupService)
    {
        $this->service = $roleService;
        $this->permissionService = $permissionService;
        $this->permissionGroupService = $permissionGroupService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate("roles");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("roles")],
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
        $permissionGroups = $this->permissionGroupService->getAll();
        return view('landlord.auth.roles.editor', compact('permissions', 'permissionGroups'));
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
        $permissionGroups = $this->permissionGroupService->getAll();
        $row = $this->service->get($id);
        if ($row) {
            $row->load('permissionGroups');
        }
        return view('landlord.auth.roles.editor', compact('row', 'permissions', 'permissionGroups'));
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

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.roles', only: ['index', 'show']),
            new Middleware('permission:create.roles', only: ['create', 'store']),
            new Middleware('permission:update.roles', only: ['edit', 'update']),
            new Middleware('permission:delete.roles', only: ['destroy']),
            new Middleware('permission:restore.roles', only: ['restore']),
        ];
    }
}
