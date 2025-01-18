<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Development\Services\ConfigurationService;
use Illuminate\Http\Request;
use Modules\Utilities\Services\TypeService;

class ConfigurationController extends ApiController
{
    protected $service;
    protected $typeService;
    public function __construct(ConfigurationService $service, TypeService $typeService)
    {
        $this->typeService = $typeService;
        $this->service = $service;
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
                    'data-modal-link' => route('landlord.development.configurations.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.developments.configurations.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $types = $this->typeService->getAll();
        return view('landlord.developments.configurations.editor', compact('types'));
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
        $types = $this->typeService->getAll();
        $row = $this->service->get($id);
        return view('landlord.developments.configurations.editor', compact('row', 'types'));
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
