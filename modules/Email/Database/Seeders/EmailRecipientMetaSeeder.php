<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailRecipientMeta;
use Modules\Email\Entities\EmailRecipient;
use Illuminate\Support\Facades\DB;

class EmailRecipientMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing recipients
        $recipients = EmailRecipient::all();

        if ($recipients->isEmpty()) {
            $this->command->warn('EmailRecipientMetaSeeder: No email recipients found. Please run EmailRecipientSeeder first.');
            return;
        }

        $metaData = [];

        foreach ($recipients as $recipient) {
            // Create 2-5 meta entries per recipient
            $metaCount = rand(2, 5);
            
            for ($i = 0; $i < $metaCount; $i++) {
                $metaKey = $this->getRandomMetaKey();
                $metaValue = $this->getRandomMetaValue($metaKey);
                
                $metaData[] = [
                    'email_recipient_id' => $recipient->id,
                    'meta_key' => $metaKey,
                    'meta_value' => $metaValue,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ];
            }
        }

        foreach ($metaData as $meta) {
            EmailRecipientMeta::firstOrCreate(
                [
                    'email_recipient_id' => $meta['email_recipient_id'],
                    'meta_key' => $meta['meta_key']
                ], 
                $meta
            );
        }

        $this->command->info('EmailRecipientMetaSeeder: Created ' . count($metaData) . ' email recipient meta entries.');
    }

    private function getRandomMetaKey(): string
    {
        $keys = [
            'source',
            'signup_date',
            'last_activity',
            'preferences',
            'location',
            'company',
            'role',
            'industry',
            'subscription_type',
            'user_agent',
            'ip_address',
            'referrer',
            'campaign_source',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'language',
            'timezone',
            'device_type',
            'browser',
            'os',
            'country',
            'city',
            'age_group',
            'gender',
            'interests',
            'tags',
            'notes',
            'priority',
            'status_reason'
        ];

        return $keys[array_rand($keys)];
    }

    private function getRandomMetaValue(string $key): string
    {
        $values = [
            'source' => ['website', 'mobile_app', 'api', 'import', 'referral', 'social_media', 'email_campaign', 'event'],
            'signup_date' => [now()->subDays(rand(1, 365))->format('Y-m-d H:i:s')],
            'last_activity' => [now()->subDays(rand(1, 30))->format('Y-m-d H:i:s')],
            'preferences' => ['{"newsletter": true, "promotions": false, "updates": true}'],
            'location' => ['New York, NY', 'London, UK', 'Tokyo, Japan', 'Sydney, Australia', 'Berlin, Germany'],
            'company' => ['Tech Corp', 'Startup Inc', 'Enterprise Ltd', 'Innovation Co', 'Digital Solutions'],
            'role' => ['CEO', 'CTO', 'Developer', 'Manager', 'Analyst', 'Designer', 'Marketing', 'Sales'],
            'industry' => ['Technology', 'Finance', 'Healthcare', 'Education', 'Retail', 'Manufacturing'],
            'subscription_type' => ['free', 'basic', 'premium', 'enterprise', 'trial'],
            'user_agent' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
            'ip_address' => ['192.168.1.1', '10.0.0.1', '172.16.0.1', '203.0.113.1', '198.51.100.1'],
            'referrer' => ['google.com', 'facebook.com', 'twitter.com', 'linkedin.com', 'direct'],
            'campaign_source' => ['summer_sale', 'product_launch', 'newsletter', 'webinar', 'trial'],
            'utm_source' => ['google', 'facebook', 'twitter', 'linkedin', 'email'],
            'utm_medium' => ['cpc', 'social', 'email', 'organic', 'referral'],
            'utm_campaign' => ['brand_awareness', 'lead_generation', 'retargeting', 'conversion'],
            'language' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko'],
            'timezone' => ['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo', 'Australia/Sydney'],
            'device_type' => ['desktop', 'mobile', 'tablet'],
            'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'],
            'os' => ['Windows', 'macOS', 'Linux', 'iOS', 'Android'],
            'country' => ['US', 'UK', 'CA', 'AU', 'DE', 'FR', 'IT', 'ES', 'JP', 'CN'],
            'city' => ['New York', 'London', 'Toronto', 'Sydney', 'Berlin', 'Paris', 'Rome', 'Madrid'],
            'age_group' => ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'],
            'gender' => ['male', 'female', 'other', 'prefer_not_to_say'],
            'interests' => ['technology', 'business', 'health', 'education', 'entertainment', 'sports'],
            'tags' => ['vip', 'premium', 'trial', 'churned', 'active', 'inactive'],
            'notes' => ['High-value customer', 'Frequent user', 'Support ticket', 'Feature request'],
            'priority' => ['low', 'medium', 'high', 'urgent'],
            'status_reason' => ['active', 'inactive', 'bounced', 'unsubscribed', 'complained']
        ];

        if (isset($values[$key])) {
            return is_array($values[$key]) ? $values[$key][array_rand($values[$key])] : $values[$key];
        }

        return 'default_value';
    }
}
