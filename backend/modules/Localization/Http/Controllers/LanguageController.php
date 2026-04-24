<?php

namespace Modules\Localization\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Localization\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class LanguageController extends ApiController implements HasMiddleware
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
                    'data-modal-link' => route('landlord.languages.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.localizations.languages.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $directionOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "direction");
        return view('landlord.localizations.languages.editor', compact('directionOptions'));
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
        $directionOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "direction");
        $row = $this->service->get($id);
        return view('landlord.localizations.languages.editor', compact('row', 'directionOptions'));
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

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.languages', only: ['index', 'show']),
            new Middleware('permission:create.languages', only: ['create', 'store']),
            new Middleware('permission:update.languages', only: ['edit', 'update']),
            new Middleware('permission:delete.languages', only: ['destroy']),
            new Middleware('permission:restore.languages', only: ['restore']),
        ];
    }
}
