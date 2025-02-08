<?php

namespace Modules\Email\Repositories;

use App\Constants\EmailType;
use App\Helpers\FileHelper;
use App\Helpers\TableHelper;
use DataTables;
use DB;
use Exception;
use Gate;
use Modules\Email\Entities\EmailCredential;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Entities\EmailAttachment;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Entities\EmailRecipientMeta;
use Modules\Email\Entities\EmailSubscriber;
use Modules\Email\Entities\EmailTemplateLog;
use Modules\Email\Services\EmailRecipientService;
use Modules\Email\Services\EmailSubscriberService;
use Modules\Email\Services\EmailTemplateService;

class EmailRepository implements EmailInterface
{
    protected $service;
    protected $emailRecipientService;
    protected $emailSubscriberService;
    protected $emailTemplateService;

    public function __construct(EmailRecipientService $emailRecipientService, EmailSubscriberService $emailSubscriberService, EmailTemplateService $emailTemplateService)
    {
        $this->emailRecipientService = $emailRecipientService;
        $this->emailSubscriberService = $emailSubscriberService;
        $this->emailTemplateService = $emailTemplateService;
    }

    public function datatables()
    {
        $rows = EmailLog::query()
            ->leftJoin('email_template_logs', 'email_template_logs.id', 'email_logs.email_template_log_id')
            ->withTrashed()
            ->select([
                'email_logs.*',
                'email_template_logs.name as template_name'
            ])->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, 'email_logs', [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                $actionButtons = "";

                $actionButtons .= '<button type="button" data-modal-title="' . translate("email_details") . '" data-modal-link="' . route('landlord.emails.show', $row->id) . '" class="btn-info btn-sm open-details-btn mx-1">';
                $actionButtons .=  '<i class="fas fa-info-circle"></i> ' . translate('details');
                $actionButtons .= '</button>';

                if (Gate::allows('resend.emails')) {
                    $actionButtons .= '<button type="button" data-route="' . route('landlord.emails.resend', $row->id) . '" class="btn-primary btn-sm resend-email-btn">';
                    $actionButtons .=  '<i class="far fa-paper-plane"></i> ' . translate('resend');
                    $actionButtons .= '</button>';
                }

                return $actionButtons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getById(int $id)
    {
        $email = EmailLog::leftJoin('email_template_logs', 'email_template_logs.id', 'email_logs.email_template_log_id')
            ->leftJoin('email_credentials', 'email_credentials.id', 'email_logs.email_credential_id')
            ->select([
                'email_logs.*',
                'email_credentials.from_address AS email_from',
                'email_template_logs.name as template_name'
            ])->where('email_logs.id', $id)->first();

        $email->attachments = $this->getEmailAttachments($email->email_template_log_id);
        return $email;
    }

    public function getEmailAttachments($emailLogId)
    {
        return EmailAttachment::leftJoin('files', 'files.id', 'email_attachments.file_id')
            ->leftJoin('folders', 'folders.id', 'files.folder_id')
            ->select([
                'email_attachments.file_id',
                'files.*',
                'folders.name'
            ])
            ->where("email_template_log_id", $emailLogId)->get();
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
                    $this->saveSubscribersAsRecipients();
                    $emailRecipients = $this->emailRecipientService->getAll();
                    break;
                case EmailType::RECIPIENTS_ONLY:
                    $emailRecipients = $this->emailRecipientService->getAll();
                    break;
                case EmailType::UPLOAD_EXCEL:
                    $emailRecipients = $this->saveRecipientsFromExcel($data);
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

    public function saveSubscribersAsRecipients()
    {
        $subscribers = EmailSubscriber::where('status', 'active')->get();

        foreach ($subscribers as $subscriber) {
            EmailRecipient::firstOrCreate(['email' => $subscriber->email]);
        }
    }

    public function saveRecipientsFromExcel($data)
    {
        $excelEmailRecipients = [];

        $excelNames = $data['excel_names'] ?? null;
        foreach ($data['excel_emails'] as $key => $excelEmail) {
            $excelEmailRecipient = EmailRecipient::firstOrCreate(['email' => $excelEmail]);
            if ($excelNames && isset($excelNames[$key])) {
                EmailRecipientMeta::firstOrCreate([
                    'email_recipient_id' => $excelEmailRecipient->id,
                    'meta_key' => 'name',
                    'meta_value' => $excelNames[$key],
                ]);
            }

            $excelEmailRecipients[] = $excelEmailRecipient;
        }
        
        return $excelEmailRecipients;
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
        return preg_replace_callback('/\{\{(\w+)\}\}/', function ($matches) use ($metadata) {
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
