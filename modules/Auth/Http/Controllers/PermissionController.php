<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PermissionController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(PermissionService $permissionService)
    {
        $this->service = $permissionService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate("permissions");
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => $title],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate("permission"),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.permissions.create'),
                    'data-modal-title' => translate("create") . " " . translate("permission"),
                ]
            ],
        ];

        return view('landlord.auth.permissions.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.auth.permissions.editor');
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
        return view('landlord.auth.permissions.editor', compact('row'));
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
            new Middleware('permission:read.permissions', only: ['index', 'show']),
            new Middleware('permission:create.permissions', only: ['create', 'store']),
            new Middleware('permission:update.permissions', only: ['edit', 'update']),
            new Middleware('permission:delete.permissions', only: ['destroy']),
            new Middleware('permission:restore.permissions', only: ['restore']),
        ];
    }
}
