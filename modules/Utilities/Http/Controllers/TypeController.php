<?php

namespace Modules\Utilities\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Utilities\Services\TypeService;
use Illuminate\Http\Request;

class TypeController extends ApiController
{
    protected $service;

    public function __construct(TypeService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = $this->service->model->pluralTitle;
        $breadcrumbs = [
            ['text' => 'Home', 'link' => route('home')],
            ['text' => $this->service->model->pluralTitle],
        ];

        $actionButtons = [
            [
                'text' => 'Add ' . $this->service->model->singleTitle,
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.types.create'),
                    'data-modal-title' => "Create " . $this->service->model->singleTitle,
                ]
            ],
        ];

        return view('landlord.utilities.types.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable("types", "status");
        return view('landlord.utilities.types.editor', compact('statusOptions'));
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
        $statusOptions = EnumHelper::getEnumFromTable("types", "status");
        $row = $this->service->get($id);
        return view('landlord.utilities.types.editor', compact('row', 'statusOptions'));
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
