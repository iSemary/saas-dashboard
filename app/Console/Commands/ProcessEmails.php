<?php

namespace App\Console\Commands;

use App\Jobs\SendBaseEmail;
use Exception;
use Illuminate\Console\Command;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Repositories\EmailRepository;

class ProcessEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails for the pending email logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $this->performSendingEmails();
            $this->info('Process Emails executed successfully.');
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Perform the operation
     */
    private function performSendingEmails(): void
    {
        EmailLog::where("status", "inactive")->chunk(100, function ($emails) {
            foreach ($emails as $email) {
                dispatch(new SendBaseEmail([
                    'log_id' => $email->id,
                    'email_credential_id' => $email->email_credential_id,
                    'email' => $email->email,
                    'subject' => $email->subject,
                    'body' => $email->body,
                    'attachments' => app(EmailRepository::class)->getEmailAttachments($email->id)
                ]));
            }
        });
    }
}
