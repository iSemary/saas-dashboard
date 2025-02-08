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

    public function index()
    {
        if (request()->ajax()) {
            return $this->emailService->getDataTables();
        }
        $title = translate('email_logs');

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("email_logs")],
        ];

        $actionButtons = [
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

        return view('landlord.emails.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function show(int $id)
    {
        $data = $this->emailService->getById($id);
        return view('landlord.emails.details', ['data' => $data]);
    }

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
        if (isset($response['status']) && $response['status']) {
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
