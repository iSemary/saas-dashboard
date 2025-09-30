<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Entities\EmailTemplate;
use Carbon\Carbon;

class EmailCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = EmailTemplate::all();

        if ($templates->isEmpty()) {
            $this->command->warn('No email templates found. Skipping email campaign seeding.');
            return;
        }

        $emailCampaigns = [
            [
                'email_template_id' => $templates->where('name', 'Welcome Email')->first()?->id ?? $templates->first()->id,
                'name' => 'New User Welcome Campaign',
                'subject' => 'Welcome to Our Platform!',
                'body' => 'Welcome to our platform! We are excited to have you on board.',
                'status' => 'active',
                'scheduled_at' => Carbon::now()->addDays(1),
            ],
            [
                'email_template_id' => $templates->where('name', 'Newsletter')->first()?->id ?? $templates->first()->id,
                'name' => 'Monthly Newsletter Campaign',
                'subject' => 'Monthly Newsletter - ' . Carbon::now()->format('F Y'),
                'body' => 'Check out our latest updates and features in this month\'s newsletter.',
                'status' => 'active',
                'scheduled_at' => Carbon::now()->addDays(7),
            ],
            [
                'email_template_id' => $templates->where('name', 'Newsletter')->first()?->id ?? $templates->first()->id,
                'name' => 'Product Launch Campaign',
                'subject' => 'New Product Launch - Exciting Features!',
                'body' => 'We are excited to announce our new product with amazing features.',
                'status' => 'inactive',
                'scheduled_at' => Carbon::now()->addDays(14),
            ],
            [
                'email_template_id' => $templates->where('name', 'Newsletter')->first()?->id ?? $templates->first()->id,
                'name' => 'Holiday Promotion Campaign',
                'subject' => 'Special Holiday Offer - Limited Time!',
                'body' => 'Don\'t miss our special holiday promotion with exclusive discounts.',
                'status' => 'active',
                'scheduled_at' => Carbon::now()->addDays(30),
            ],
            [
                'email_template_id' => $templates->where('name', 'Newsletter')->first()?->id ?? $templates->first()->id,
                'name' => 'Feature Update Campaign',
                'subject' => 'New Features Available Now!',
                'body' => 'Discover the latest features we\'ve added to improve your experience.',
                'status' => 'active',
                'scheduled_at' => Carbon::now()->addDays(3),
            ],
        ];

        foreach ($emailCampaigns as $campaignData) {
            EmailCampaign::create($campaignData);
        }

        $this->command->info('Email campaigns seeded successfully!');
    }
}
