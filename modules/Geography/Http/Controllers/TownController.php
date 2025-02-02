<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\TownService;
use Illuminate\Http\Request;
use Modules\Geography\Services\CityService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TownController extends ApiController implements HasMiddleware
{
    protected $service;
    protected $cityService;

    public function __construct(TownService $service, CityService $cityService)
    {
        $this->service = $service;
        $this->cityService = $cityService;
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
                    'data-modal-link' => route('landlord.towns.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.geography.towns.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $cities = $this->cityService->getAll();
        return view('landlord.geography.towns.editor', compact('cities'));
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
        $cities = $this->cityService->getAll();
        $row = $this->service->get($id);
        return view('landlord.geography.towns.editor', compact('row', 'cities'));
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
            new Middleware('permission:read.towns', only: ['index', 'show']),
            new Middleware('permission:create.towns', only: ['create', 'store']),
            new Middleware('permission:update.towns', only: ['edit', 'update']),
            new Middleware('permission:delete.towns', only: ['destroy']),
            new Middleware('permission:restore.towns', only: ['restore']),
        ];
    }
}
