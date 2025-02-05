<?php

namespace Modules\Email\Repositories;

use App\Constants\EmailType;
use App\Helpers\FileHelper;
use DB;
use Exception;
use Modules\Email\Entities\EmailCredential;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Entities\EmailAttachment;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Entities\EmailRecipientMeta;
use Modules\Email\Entities\EmailTemplateLog;
use Modules\Email\Services\EmailRecipientService;
use Modules\Email\Services\EmailSubscriberService;
use Modules\Email\Services\EmailTemplateService;

class EmailRepository implements EmailInterface
{
    protected $emailRecipientService;
    protected $emailSubscriberService;
    protected $emailTemplateService;

    public function __construct(EmailRecipientService $emailRecipientService, EmailSubscriberService $emailSubscriberService, EmailTemplateService $emailTemplateService)
    {
        $this->emailRecipientService = $emailRecipientService;
        $this->emailSubscriberService = $emailSubscriberService;
        $this->emailTemplateService = $emailTemplateService;
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
    public function send(array $data)
    {

        try {
            DB::beginTransaction();

            $emailTemplate = null;
            if ($data['email_template_id']) {
                $emailTemplate = $this->emailTemplateService->get($data['email_template_id']);
            }

            // Save current details as email template log [even if it's not email template]
            $emailTemplateLog = EmailTemplateLog::create([
                'name' => $emailTemplate ? $emailTemplate->name : '-',
                'subject' => $data['subject'],
                'body' => $data['body'],
            ]);
            $campaign = null;
            // Save attachment files
            if (isset($data['files']) && count($data['files'])) {
                $attachments = $data['files'];
                foreach ($attachments as $attachment) {
                    if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                        // upload the file
                        $file = app(FileHelper::class)->directUpload($attachment, 'emails');
                        // create email attachment row
                        EmailAttachment::create([
                            'email_campaign_id' => $campaign ? $campaign->id : null,
                            'email_template_log_id' => $emailTemplateLog->id,
                            'file_id' => $file->id,
                        ]);
                    }
                }
            }


            // Collect recipients as array [id, email, metadata] 
            $emailRecipients = [];
            switch ($data['recipients_type']) {
                case EmailType::SINGLE:
                    $emailRecipients = [EmailRecipient::firstOrCreate([
                        'email' => $data['email']
                    ])];
                    break;
                case EmailType::MULTIPLE:
                    $emailRecipients = EmailRecipient::whereIn("email", $data['emails'])->get();
                    break;
                case EmailType::ALL_USERS:
                    // $this->emailRecipientService->all() + $this->emailSubscriberService->all();
                    // $emails = [$data['email']];
                    break;
                case EmailType::RECIPIENTS_ONLY:
                    $emailRecipients = $this->emailRecipientService->all();
                    break;
                case EmailType::UPLOAD_EXCEL:
                    $emails = [$data['email']];
                    break;
                default:
                    break;
            }

            // Save the log
            foreach ($emailRecipients as $emailRecipient) {
                $emailRecipientMeta = EmailRecipientMeta::where("email_recipient_id", $emailRecipient->id)->get();

                $formattedEmail = $this->formatEmailMetadata($emailRecipientMeta, $data['subject'], $data['body']);

                EmailLog::create([
                    'email_recipient_id' => $emailRecipient->id,
                    'email_template_log_id' => $emailTemplateLog->id,
                    'email_credential_id' => $data['email_credential_id'],
                    'email_campaign_id' => $campaign ? $campaign->id : null,
                    'email' => $emailRecipient->email,
                    'status' => 'inactive',
                    'subject' => $emailRecipientMeta ? $formattedEmail['subject'] : $data['subject'],
                    'body' => $emailRecipientMeta ? $formattedEmail['body'] : $data['body'],
                    'email_recipient_meta' => $emailRecipientMeta ? $formattedEmail['metadata'] : null,
                ]);
            }

            // Commit the transactions
            DB::commit();

            // Send emails by the job

            return ['success' => true];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function formatEmailMetadata($emailRecipientMeta, $subject, $body): array
    {
        // Create a metadata key-value map
        $metadataMap = $emailRecipientMeta->pluck('meta_value', 'meta_key')->toArray();

        // Replace placeholders in subject and body
        $formattedSubject = $this->replacePlaceholders($subject, $metadataMap);
        $formattedBody = $this->replacePlaceholders($body, $metadataMap);

        return [
            'subject' => $formattedSubject,
            'body' => $formattedBody,
            'metadata' => $metadataMap ? json_encode($metadataMap) : null,
        ];
    }

    private function replacePlaceholders($template, $metadata): string
    {
        return preg_replace_callback('/\{\{(\w+)\}\}/', function($matches) use ($metadata) {
            $key = $matches[1];
            return $metadata[$key] ?? '';
        }, $template);
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
