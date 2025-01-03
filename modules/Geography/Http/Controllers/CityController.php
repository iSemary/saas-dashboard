<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\CityService;
use Illuminate\Http\Request;
use Modules\Geography\Services\ProvinceService;

class CityController extends ApiController
{
    protected $service;
    protected $provinceService;

    public function __construct(CityService $service, ProvinceService $provinceService)
    {
        $this->service = $service;
        $this->provinceService = $provinceService;
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
                    'data-modal-link' => route('landlord.cities.create'),
                    'data-modal-title' => "Create " . $this->service->model->singleTitle,
                ]
            ],
        ];

        return view('landlord.geography.cities.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $provinces = $this->provinceService->getAll();
        return view('landlord.geography.cities.editor', compact('provinces'));
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
        $provinces = $this->provinceService->getAll();
        return view('landlord.geography.cities.editor', compact('row', 'provinces'));
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
