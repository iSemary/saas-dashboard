<?php

namespace Modules\Email\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class EmailTemplateController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(EmailTemplateService $service)
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
                    'data-modal-link' => route('landlord.email-templates.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
            [
                'text' => translate("compose"),
                'permission' => 'send.emails',
                'class' => 'btn-sm btn-orange text-white open-details-btn compose-email-btn',
                'icon' => '<i class="bi bi-plus-circle-dotted"></i>',
                'attr' => [
                    'data-modal-link' => route('landlord.emails.compose'),
                    'data-modal-title' => translate("compose"),
                ]
            ]
        ];

        return view('landlord.emails.email-templates.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.emails.email-templates.editor', compact('statusOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, translate("created_successfully"));
    }

    public function show(int $id)
    {
        return $this->return(200, 'Template fetched successfully', ['data' => $this->service->get($id)]);
    }

    public function edit($id)
    {
        $row = $this->service->get($id);
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.emails.email-templates.editor', compact('row', 'statusOptions'));
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
            new Middleware('permission:read.email_templates', only: ['index', 'show']),
            new Middleware('permission:create.email_templates', only: ['create', 'store']),
            new Middleware('permission:update.email_templates', only: ['edit', 'update']),
            new Middleware('permission:delete.email_templates', only: ['destroy']),
            new Middleware('permission:restore.email_templates', only: ['restore']),
        ];
    }
}
