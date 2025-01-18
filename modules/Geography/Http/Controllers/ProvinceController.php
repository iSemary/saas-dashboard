<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\ProvinceService;
use Illuminate\Http\Request;
use Modules\Geography\Services\CountryService;

class ProvinceController extends ApiController
{
    protected $service;
    protected $countryService;

    public function __construct(ProvinceService $service, CountryService $countryService)
    {
        $this->service = $service;
        $this->countryService = $countryService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate($this->service->model->pluralTitle);
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.provinces.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.geography.provinces.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $countries = $this->countryService->getAll();
        return view('landlord.geography.provinces.editor', compact('countries'));
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
        $countries = $this->countryService->getAll();
        $row = $this->service->get($id);
        return view('landlord.geography.provinces.editor', compact('row', 'countries'));
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
