<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends ApiController
{
    protected $service;

    public function __construct(CountryService $service)
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
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => 'Add ' . $this->service->model->singleTitle,
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.countries.create'),
                    'data-modal-title' => "Create " . $this->service->model->singleTitle,
                ]
            ],
        ];

        return view('landlord.geography.countries.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.geography.countries.editor');
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
        return view('landlord.geography.countries.editor', compact('row'));
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
