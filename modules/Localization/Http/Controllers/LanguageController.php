<?php

namespace Modules\Localization\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Localization\Services\LanguageService;
use Illuminate\Http\Request;

class LanguageController extends ApiController
{
    protected $service;

    public function __construct(LanguageService $service)
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
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.languages.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.localizations.languages.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $directionOptions = EnumHelper::getEnumFromTable("languages", "direction");
        return view('landlord.localizations.languages.editor', compact('directionOptions'));
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
        $directionOptions = EnumHelper::getEnumFromTable("languages", "direction");
        $row = $this->service->get($id);
        return view('landlord.localizations.languages.editor', compact('row', 'directionOptions'));
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
