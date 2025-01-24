<?php

namespace Modules\Email\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailCredentialService;
use Modules\Email\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Modules\Email\Services\EmailService;

class EmailController extends ApiController
{
    protected $emailService;
    protected $emailCredentialService;
    protected $emailTemplateService;

    public function __construct(EmailService $emailService, EmailCredentialService $emailCredentialService, EmailTemplateService $emailTemplateService)
    {
        $this->emailService = $emailService;
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

    public function send(Request $request)
    {
        $this->emailService->send($request->all());
        return $this->return(200, translate('email_sent_successfully'));
    }
}
