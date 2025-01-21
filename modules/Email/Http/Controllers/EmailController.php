<?php

namespace Modules\Email\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailCredentialService;
use Modules\Email\Services\EmailTemplateService;

class EmailController extends ApiController
{
    protected $emailCredentialService;
    protected $emailTemplateService;

    public function __construct(EmailCredentialService $emailCredentialService, EmailTemplateService $emailTemplateService)
    {
        $this->emailCredentialService = $emailCredentialService;
        $this->emailTemplateService = $emailTemplateService;
    }

    public function index() {}

    public function compose()
    {
        $emailCredentials = $this->emailCredentialService->getAll(['status' => 'active']);
        $emailTemplates = $this->emailTemplateService->getAll(['status' => 'active']);
        return view("landlord.emails.compose", ['emailCredentials' => $emailCredentials, 'emailTemplates' => $emailTemplates]);
    }

    public function resend() {}
}
