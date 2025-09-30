<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailLog;
use Modules\Email\Entities\EmailTemplateLog;
use Modules\Email\Entities\EmailCredential;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Entities\EmailRecipient;
use Illuminate\Support\Facades\DB;

class EmailLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing records for relationships
        $templateLogs = EmailTemplateLog::all();
        $credentials = EmailCredential::all();
        $campaigns = EmailCampaign::all();
        $recipients = EmailRecipient::all();

        if ($templateLogs->isEmpty() || $credentials->isEmpty()) {
            $this->command->warn('EmailLogSeeder: Required related records not found. Please run EmailTemplateLogSeeder and EmailCredentialSeeder first.');
            return;
        }

        $emailLogs = [
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'john.doe@example.com',
                'status' => 'sent',
                'subject' => 'Welcome to Our Platform',
                'body' => '<h1>Welcome!</h1><p>Thank you for joining our platform.</p>',
                'email_recipient_meta' => json_encode(['source' => 'registration', 'campaign' => 'welcome']),
                'opened_at' => now()->subHours(2),
                'clicked_at' => now()->subHour(),
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHour(),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'jane.smith@company.com',
                'status' => 'sent',
                'subject' => 'Password Reset Request',
                'body' => '<h1>Password Reset</h1><p>Click the link to reset your password.</p>',
                'email_recipient_meta' => json_encode(['source' => 'password_reset', 'ip' => '192.168.1.1']),
                'opened_at' => now()->subMinutes(30),
                'clicked_at' => null,
                'created_at' => now()->subHour(),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'marketing@startup.io',
                'status' => 'failed',
                'subject' => 'Monthly Newsletter',
                'body' => '<h1>Monthly Newsletter</h1><p>Here are the latest updates...</p>',
                'email_recipient_meta' => json_encode(['source' => 'newsletter', 'segment' => 'premium']),
                'error_message' => 'SMTP connection timeout',
                'opened_at' => null,
                'clicked_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'support@techcorp.com',
                'status' => 'sent',
                'subject' => 'Account Verification',
                'body' => '<h1>Verify Your Account</h1><p>Please verify your email address.</p>',
                'email_recipient_meta' => json_encode(['source' => 'verification', 'user_id' => 123]),
                'opened_at' => now()->subDays(1),
                'clicked_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'admin@enterprise.com',
                'status' => 'processing',
                'subject' => 'System Maintenance Notice',
                'body' => '<h1>Maintenance Notice</h1><p>System will be down for maintenance.</p>',
                'email_recipient_meta' => json_encode(['source' => 'system', 'priority' => 'high']),
                'opened_at' => null,
                'clicked_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'billing@saas.com',
                'status' => 'sent',
                'subject' => 'Invoice #INV-2024-001',
                'body' => '<h1>Invoice</h1><p>Your monthly invoice is ready.</p>',
                'email_recipient_meta' => json_encode(['source' => 'billing', 'invoice_id' => 'INV-2024-001']),
                'opened_at' => now()->subHours(6),
                'clicked_at' => now()->subHours(5),
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(5),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'developer@startup.com',
                'status' => 'sent',
                'subject' => 'API Documentation Update',
                'body' => '<h1>API Update</h1><p>New API endpoints are available.</p>',
                'email_recipient_meta' => json_encode(['source' => 'api_update', 'version' => '2.1']),
                'opened_at' => now()->subDays(3),
                'clicked_at' => now()->subDays(2),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(2),
            ],
            [
                'email_template_log_id' => $templateLogs->random()->id,
                'email_credential_id' => $credentials->random()->id,
                'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                'email_recipient_id' => $recipients->isNotEmpty() ? $recipients->random()->id : null,
                'email' => 'sales@company.com',
                'status' => 'sent',
                'subject' => 'Product Launch Announcement',
                'body' => '<h1>New Product Launch</h1><p>Check out our latest product features.</p>',
                'email_recipient_meta' => json_encode(['source' => 'product_launch', 'product' => 'Pro Plan']),
                'opened_at' => now()->subWeek(),
                'clicked_at' => now()->subWeek(),
                'created_at' => now()->subWeek(),
                'updated_at' => now()->subWeek(),
            ],
        ];

        foreach ($emailLogs as $log) {
            EmailLog::firstOrCreate(
                [
                    'email' => $log['email'], 
                    'subject' => $log['subject'], 
                    'created_at' => $log['created_at']
                ], 
                $log
            );
        }

        $this->command->info('EmailLogSeeder: Created ' . count($emailLogs) . ' email logs with rich data and relationships.');
    }
}
