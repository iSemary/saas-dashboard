<?php

namespace Modules\Email\Http\Controllers;

use App\Constants\EmailType;
use App\Http\Controllers\ApiController;
use Exception;
use Modules\Email\Services\EmailCredentialService;
use Modules\Email\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Modules\Email\Services\EmailService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use ReflectionClass;

class EmailController extends ApiController implements HasMiddleware
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
        $emailTypes = (new ReflectionClass(EmailType::class))->getConstants();
        return view("landlord.emails.compose", ['emailCredentials' => $emailCredentials, 'emailTemplates' => $emailTemplates, 'emailTypes' => $emailTypes]);
    }

    public function resend() {}

    public function send(Request $request)
    {
        $response = $this->emailService->send($request->all());
        if ($response['status']) {
            return $this->return(200, translate('email_sent_successfully'), debug: $response ?? null);
        }
        return $this->return(200, translate('something_went_wrong'), debug: $response ?? null);
    }

    // Recipients and Subscribers
    public function countAll()
    {
        $count = $this->emailService->countAllEmails();
        return $this->return(200, 'All Recipients Fetched', ['count' => $count]);
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.email_logs', only: ['index']),
            new Middleware('permission:send.emails', only: ['send']),
            new Middleware('permission:resend.email_logs', only: ['resend']),
        ];
    }
}
