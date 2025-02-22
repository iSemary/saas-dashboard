<?php

namespace App\Jobs;

use App\Helpers\CryptHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\BaseMail;
use Illuminate\Support\Facades\Log;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Repositories\EmailCredentialRepository;
use Modules\Tenant\Entities\Tenant;
use Spatie\Multitenancy\Jobs\TenantAware;

class SendBaseEmail implements ShouldQueue, TenantAware
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send the email

        $emailCredentials = app(EmailCredentialRepository::class)->find($this->data['email_credential_id']);

        if (!$emailCredentials) {
            EmailLog::where("id", $this->data['log_id'])->update([
                'status' => 'failed',
                'error_message' => 'Invalid email credential ID'
            ]);
            Log::error('Email sending failed: Invalid email credential ID.');
            return;
        }

        // Set up mail configuration dynamically
        $mailConfig = [
            'transport'  => $emailCredentials->mailer,
            'host'       => $emailCredentials->host,
            'port'       => $emailCredentials->port,
            'encryption' => $emailCredentials->encryption,
            'username'   => $emailCredentials->username,
            'password'   => CryptHelper::decrypt($emailCredentials->getRawOriginal('password')),
            'from'       => [
                'address' => $emailCredentials->from_address,
                'name'    => $emailCredentials->from_name,
            ],
        ];

        // Temporarily override mail configuration
        config(['mail.mailers.dynamic' => $mailConfig]);
        config(['mail.default' => 'dynamic']);

        try {
            // Send the email
            Mail::to($this->data['email'])->send(new BaseMail($this->data));

            // Update email log status
            EmailLog::where("id", $this->data['log_id'])->update(['status' => 'sent']);
        } catch (\Exception $e) {
            EmailLog::where("id", $this->data['log_id'])->update([
                'status' => 'failed',
                'error_message' => env("APP_ENV") == "local" ? $e->getMessage() : "Internal Server Error"
            ]);
            Log::error('Email sending failed: ' . $e->getMessage());
        }
    }
}
