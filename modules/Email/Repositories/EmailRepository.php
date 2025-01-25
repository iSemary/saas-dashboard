<?php

namespace Modules\Email\Repositories;

use Modules\Email\Entities\EmailCredential;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Entities\EmailAttachment;
use Modules\Email\Services\EmailRecipientService;
use Modules\Email\Services\EmailSubscriberService;

class EmailRepository implements EmailInterface
{
    protected $emailRecipientService;
    protected $emailSubscriberService;
   
    public function __construct(EmailRecipientService $emailRecipientService, EmailSubscriberService $emailSubscriberService)
    {
        $this->emailRecipientService = $emailRecipientService;
        $this->emailSubscriberService = $emailSubscriberService;
    }
    /**
     * Send email
     *
     * @param array $data
     *  email_template_id: 1
     *  email_credential_id: 2
     *  recipients_type: single|multiple|all|recipients_only|upload_excel
     *  email: abdelrahmansamirmostafa@gmail.com
     *  subject: Hello {{name}}
     *  body: <p>Hello</p> {{name}}
     *  attachments: [files or null]
     * @return void
     */
    public function send(array $data) {
        // Save current details as email template log [even if it's not email template]
        
        // Save attachment files

        // Collect recipients as array [id, email, metadata] 

        // Send the job
        
        // Save the log

    }

    public function countAllEmails()
    {
        return $this->emailRecipientService->count() + $this->emailSubscriberService->count();
    }

    public function getEmailCredential(array $data)
    {
        return EmailCredential::where('status', 'active')->get();
    }

    public function getEmailTemplate(array $data)
    {
        return EmailTemplate::where('status', 'active')->get();
    }

    public function getEmailRecipient(array $data)
    {
        return EmailRecipient::where('status', 'active')->get();
    }

    public function getEmailAttachment(array $data)
    {
        return EmailAttachment::where('status', 'active')->get();
    }
}
