<?php

namespace Modules\Email\Repositories;

use Modules\Email\Entities\EmailCredential;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Entities\EmailAttachment;

class EmailRepository implements EmailInterface
{
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
    public function send(array $data) {}

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
