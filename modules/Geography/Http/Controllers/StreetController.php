<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\StreetService;
use Illuminate\Http\Request;
use Modules\Geography\Services\TownService;

class StreetController extends ApiController
{
    protected $service;
    protected $townService;

    public function __construct(StreetService $service, TownService $townService)
    {
        $this->service = $service;
        $this->townService = $townService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = $this->service->model->pluralTitle;
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.streets.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.geography.streets.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $towns = $this->townService->getAll();
        return view('landlord.geography.streets.editor', compact('towns'));
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
        $towns = $this->townService->getAll();
        $row = $this->service->get($id);
        return view('landlord.geography.streets.editor', compact('row', 'towns'));
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
