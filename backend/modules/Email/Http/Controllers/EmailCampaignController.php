<?php

namespace Modules\Email\Http\Controllers;

use App\Constants\EmailType;
use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailCampaignService;
use Illuminate\Http\Request;
use Modules\Email\Services\EmailCredentialService;
use Modules\Email\Services\EmailTemplateService;
use Modules\Email\Services\EmailService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use ReflectionClass;

class EmailCampaignController extends ApiController implements HasMiddleware
{
    protected $service;
    protected $emailService;
    protected $emailCredentialService;
    protected $emailTemplateService;

    public function __construct(EmailCampaignService $service, EmailService $emailService, EmailCredentialService $emailCredentialService, EmailTemplateService $emailTemplateService)
    {
        $this->emailCredentialService = $emailCredentialService;
        $this->emailTemplateService = $emailTemplateService;
        $this->service = $service;
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
                    'data-modal-link' => route('landlord.email-campaigns.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.emails.email-campaigns.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $emailCredentials = $this->emailCredentialService->getAll(['status' => 'active']);
        $emailTemplates = $this->emailTemplateService->getAll(['status' => 'active']);
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        $emailTypes = (new ReflectionClass(EmailType::class))->getConstants();
        return view('landlord.emails.email-campaigns.editor', compact('statusOptions', 'emailTemplates', 'emailCredentials', 'emailTypes'));
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
        $emailCredentials = $this->emailCredentialService->getAll(['status' => 'active']);
        $emailTemplates = $this->emailTemplateService->getAll(['status' => 'active']);
        $row = $this->service->get($id);
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.emails.email-campaigns.editor', compact('row', 'statusOptions', 'emailTemplates', 'emailCredentials'));
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
            new Middleware('permission:read.email_campaigns', only: ['index', 'show']),
            new Middleware('permission:create.email_campaigns', only: ['create', 'store']),
            new Middleware('permission:update.email_campaigns', only: ['edit', 'update']),
            new Middleware('permission:delete.email_campaigns', only: ['destroy']),
            new Middleware('permission:restore.email_campaigns', only: ['restore']),
        ];
    }
}
