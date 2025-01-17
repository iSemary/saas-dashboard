<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends ApiController
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
        $title = "permissions";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => "permissions"],
        ];

        $actionButtons = [
            [
                'text' => 'Add ' . "permission",
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.permissions.create'),
                    'data-modal-title' => "Create " . "permission",
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
        return $this->return(200, "Created successfully");
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
        return $this->return(200, "Updated successfully");
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
