<?php

namespace Modules\Utilities\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Utilities\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ModuleController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(ModuleService $service)
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
                    'data-modal-link' => route('landlord.modules.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.utilities.modules.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.utilities.modules.editor', compact('statusOptions'));
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
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.utilities.modules.editor', compact('row', 'statusOptions'));
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
            new Middleware('permission:read.modules', only: ['index', 'show']),
            new Middleware('permission:create.modules', only: ['create', 'store']),
            new Middleware('permission:update.modules', only: ['edit', 'update']),
            new Middleware('permission:delete.modules', only: ['destroy']),
            new Middleware('permission:restore.modules', only: ['restore']),
        ];
    }
}
