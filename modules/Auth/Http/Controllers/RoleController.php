<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends ApiController
{
    protected $service;

    public function __construct(RoleService $roleService)
    {
        $this->service = $roleService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = "roles";
        $breadcrumbs = [
            ['text' => 'Home', 'link' => route('home')],
            ['text' => "roles"],
        ];

        $actionButtons = [
            [
                'text' => 'Add ' . "role",
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.roles.create'),
                    'data-modal-title' => "Create " . "roles",
                ]
            ],
        ];

        return view('landlord.auth.roles.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.auth.roles.editor');
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
        return view('landlord.auth.roles.editor', compact('row'));
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
