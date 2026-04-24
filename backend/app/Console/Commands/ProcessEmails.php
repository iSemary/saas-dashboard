<?php

namespace App\Console\Commands;

use App\Jobs\SendBaseEmail;
use Exception;
use Illuminate\Console\Command;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Repositories\EmailRepository;
use Modules\Tenant\Entities\Tenant;

class ProcessEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:emails {--tenant= : Process emails for a specific tenant by domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails for the pending email logs, either for a specific tenant or all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $tenantDomain = $this->option('tenant');

            if ($tenantDomain) {
                $tenant = Tenant::where('domain', $tenantDomain)->first();
                if (!$tenant) {
                    $this->error("Tenant with domain '{$tenantDomain}' not found.");
                    return;
                }
                $this->processForTenant($tenant);
            } else {
                Tenant::all()->each(fn ($tenant) => $this->processForTenant($tenant));
            }

            $this->info('Process Emails executed successfully.');
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Process email sending for a specific tenant
     */
    private function processForTenant(Tenant $tenant): void
    {
        $tenant->execute(function () use ($tenant) {
            EmailLog::where("status", "processing")->chunk(100, function ($emails) {
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
        });

        $this->info("Processed emails for tenant: {$tenant->domain}");
    }
}
