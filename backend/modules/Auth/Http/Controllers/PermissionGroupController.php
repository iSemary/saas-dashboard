<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\PermissionGroupService;
use Modules\Auth\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PermissionGroupController extends ApiController implements HasMiddleware
{
    protected $service;
    protected $permissionService;

    public function __construct(PermissionGroupService $permissionGroupService, PermissionService $permissionService)
    {
        $this->service = $permissionGroupService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $title = translate("permission_groups");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => $title],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate("permission_group"),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.permission-groups.create'),
                    'data-modal-title' => translate('create') . " " . translate("permission_group"),
                ]
            ],
        ];

        return view('landlord.auth.permission-groups.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $permissions = $this->permissionService->getAll();
        return view('landlord.auth.permission-groups.editor', compact('permissions'));
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
        return view('landlord.auth.permission-groups.editor', compact('row', 'permissions'));
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
            new Middleware('permission:read.permission_groups', only: ['index', 'show']),
            new Middleware('permission:create.permission_groups', only: ['create', 'store']),
            new Middleware('permission:update.permission_groups', only: ['edit', 'update']),
            new Middleware('permission:delete.permission_groups', only: ['destroy']),
            new Middleware('permission:restore.permission_groups', only: ['restore']),
        ];
    }
}
