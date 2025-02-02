<?php

namespace Modules\Email\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailCredentialService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class EmailCredentialController extends ApiController implements HasMiddleware
{
    protected EmailCredentialService $service;

    public function __construct(EmailCredentialService $service)
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
                    'data-modal-link' => route('landlord.email-credentials.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.emails.email-credentials.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $encryptionOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), 'encryption');
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), 'status');
        return view('landlord.emails.email-credentials.editor', ['encryptionOptions' => $encryptionOptions, 'statusOptions' => $statusOptions]);
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
        $encryptionOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), 'encryption');
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), 'status');
        $row = $this->service->get($id);
        return view('landlord.emails.email-credentials.editor', compact('row', 'encryptionOptions', 'statusOptions'));
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
            new Middleware('permission:read.email_credentials', only: ['index', 'show']),
            new Middleware('permission:create.email_credentials', only: ['create', 'store']),
            new Middleware('permission:update.email_credentials', only: ['edit', 'update']),
            new Middleware('permission:delete.email_credentials', only: ['destroy']),
            new Middleware('permission:restore.email_credentials', only: ['restore']),
        ];
    }
}
