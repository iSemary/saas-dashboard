<?php

namespace Modules\Geography\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Geography\Services\CityService;
use Illuminate\Http\Request;
use Modules\Geography\Services\ProvinceService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CityController extends ApiController implements HasMiddleware
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
                    'data-modal-link' => route('landlord.cities.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
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
        return $this->return(200, translate("created_successfully"));
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
        return $this->return(200, "Restored successfully");
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.cities', only: ['index', 'show']),
            new Middleware('permission:create.cities', only: ['create', 'store']),
            new Middleware('permission:update.cities', only: ['edit', 'update']),
            new Middleware('permission:delete.cities', only: ['destroy']),
            new Middleware('permission:restore.cities', only: ['restore']),
        ];
    }
}
