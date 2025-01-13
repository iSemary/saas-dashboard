<?php

namespace Modules\Utilities\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Utilities\Services\TagService;
use Illuminate\Http\Request;

class TagController extends ApiController
{
    protected $service;

    public function __construct(TagService $service)
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
                    'data-modal-link' => route('landlord.tags.create'),
                    'data-modal-title' => "Create " . $this->service->model->singleTitle,
                ]
            ],
        ];

        return view('landlord.utilities.tags.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable("tags", "status");
        return view('landlord.utilities.tags.editor', compact("statusOptions"));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, "Created successfully");
    }

    public function show($id)
    {
        if (request()->ajax() && request()->get('table')) {
            return $this->service->getDataTables($id);
        }

        return view("landlord.utilities.tags.show", compact('id'));
    }

    public function edit($id)
    {
        $statusOptions = EnumHelper::getEnumFromTable("tags", "status");
        $row = $this->service->get($id);
        return view('landlord.utilities.tags.editor', compact('row', 'statusOptions'));
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
