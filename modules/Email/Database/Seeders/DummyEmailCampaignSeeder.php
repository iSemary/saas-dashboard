<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Entities\EmailTemplate;

class DummyEmailCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing email templates
        $welcomeTemplate = EmailTemplate::where('name', 'Welcome Email')->first();
        $passwordResetTemplate = EmailTemplate::where('name', 'Password Reset')->first();

        if (!$welcomeTemplate || !$passwordResetTemplate) {
            $this->command->warn('Email templates not found. Please run EmailTemplateSeeder first.');
            return;
        }

        $dummyCampaigns = [
            [
                'email_template_id' => $welcomeTemplate->id,
                'name' => 'Welcome New Users Campaign',
                'subject' => 'Welcome to Our Platform!',
                'body' => '<h1>Welcome to Our Platform!</h1><p>Thank you for joining us. We are excited to have you on board!</p>',
                'status' => 'active',
                'scheduled_at' => now()->addDays(1),
            ],
            [
                'email_template_id' => $passwordResetTemplate->id,
                'name' => 'Password Reset Campaign',
                'subject' => 'Reset Your Password',
                'body' => '<h1>Password Reset Request</h1><p>Click the link below to reset your password.</p>',
                'status' => 'active',
                'scheduled_at' => now()->addHours(2),
            ],
            [
                'email_template_id' => $welcomeTemplate->id,
                'name' => 'Monthly Newsletter',
                'subject' => 'Monthly Newsletter - Updates and News',
                'body' => '<h1>Monthly Newsletter</h1><p>Here are the latest updates and news from our platform.</p>',
                'status' => 'inactive',
                'scheduled_at' => now()->addDays(30),
            ],
            [
                'email_template_id' => $welcomeTemplate->id,
                'name' => 'Product Launch Announcement',
                'subject' => 'New Product Launch - Check It Out!',
                'body' => '<h1>New Product Launch</h1><p>We are excited to announce our new product. Check it out now!</p>',
                'status' => 'active',
                'scheduled_at' => now()->addDays(7),
            ],
            [
                'email_template_id' => $passwordResetTemplate->id,
                'name' => 'Security Alert Campaign',
                'subject' => 'Security Alert - Action Required',
                'body' => '<h1>Security Alert</h1><p>We detected unusual activity on your account. Please take action.</p>',
                'status' => 'active',
                'scheduled_at' => now()->addMinutes(30),
            ],
        ];

        foreach ($dummyCampaigns as $campaignData) {
            EmailCampaign::firstOrCreate(
                [
                    'name' => $campaignData['name'],
                    'subject' => $campaignData['subject']
                ],
                $campaignData
            );
        }

        $this->command->info('Dummy email campaigns seeded successfully!');
    }
}
