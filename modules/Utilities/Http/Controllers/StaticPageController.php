<?php

namespace Modules\Utilities\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Utilities\Services\StaticPageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class StaticPageController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(StaticPageService $service)
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
                    'data-modal-link' => route('landlord.static-pages.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.utilities.static-pages.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        $attributeKeys = EnumHelper::getEnumFromTable('static_page_attributes', "attribute_key");
        $attributeStatusOptions = EnumHelper::getEnumFromTable('static_page_attributes', "status");
        return view('landlord.utilities.static-pages.editor', compact('statusOptions','attributeKeys', 'attributeStatusOptions'));
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
        $attributeKeys = EnumHelper::getEnumFromTable('static_page_attributes', "attribute_key");
        $attributeStatusOptions = EnumHelper::getEnumFromTable('static_page_attributes', "status");
        return view('landlord.utilities.static-pages.editor', compact('row','attributeKeys', 'statusOptions', 'attributeStatusOptions'));
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
            new Middleware('permission:read.static_pages', only: ['index', 'show']),
            new Middleware('permission:create.static_pages', only: ['create', 'store']),
            new Middleware('permission:update.static_pages', only: ['edit', 'update']),
            new Middleware('permission:delete.static_pages', only: ['destroy']),
            new Middleware('permission:restore.static_pages', only: ['restore']),
        ];
    }
}
