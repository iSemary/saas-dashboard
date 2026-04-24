<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Entities\PlanFeature;
use Modules\Subscription\Entities\Plan;

class PlanFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = Plan::all();

        if ($plans->isEmpty()) {
            $this->command->warn('No plans found. Skipping plan feature seeding.');
            return;
        }

        $planFeatures = [
            // Free Plan features
            [
                'plan_name' => 'Free Plan',
                'name' => 'Basic Dashboard',
                'description' => 'Access to basic dashboard with essential features',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Free Plan',
                'name' => '5 Projects',
                'description' => 'Create up to 5 projects',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Free Plan',
                'name' => '1GB Storage',
                'description' => '1GB of file storage space',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Free Plan',
                'name' => 'Email Support',
                'description' => 'Email support with 48-hour response time',
                'status' => 'active',
            ],

            // Starter Plan features
            [
                'plan_name' => 'Starter Plan',
                'name' => 'Advanced Dashboard',
                'description' => 'Access to advanced dashboard with analytics',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Starter Plan',
                'name' => '25 Projects',
                'description' => 'Create up to 25 projects',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Starter Plan',
                'name' => '10GB Storage',
                'description' => '10GB of file storage space',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Starter Plan',
                'name' => 'Priority Support',
                'description' => 'Priority email support with 24-hour response time',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Starter Plan',
                'name' => 'API Access',
                'description' => 'Basic API access for integrations',
                'status' => 'active',
            ],

            // Professional Plan features
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Premium Dashboard',
                'description' => 'Access to premium dashboard with advanced analytics',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Unlimited Projects',
                'description' => 'Create unlimited projects',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => '100GB Storage',
                'description' => '100GB of file storage space',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Live Chat Support',
                'description' => 'Live chat support with 12-hour response time',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Full API Access',
                'description' => 'Full API access with rate limits',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Custom Integrations',
                'description' => 'Custom integrations and webhooks',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'name' => 'Advanced Analytics',
                'description' => 'Advanced analytics and reporting features',
                'status' => 'active',
            ],

            // Enterprise Plan features
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'Custom Dashboard',
                'description' => 'Fully customizable dashboard',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'Unlimited Everything',
                'description' => 'Unlimited projects, storage, and users',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'Dedicated Support',
                'description' => 'Dedicated account manager and 24/7 support',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'Unlimited API Access',
                'description' => 'Unlimited API access with no rate limits',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'Custom Development',
                'description' => 'Custom development and integrations',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'SLA Guarantee',
                'description' => '99.9% uptime SLA guarantee',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'name' => 'On-premise Option',
                'description' => 'On-premise deployment option',
                'status' => 'active',
            ],

            // Agency Plan features
            [
                'plan_name' => 'Agency Plan',
                'name' => 'Multi-client Management',
                'description' => 'Manage multiple clients from one dashboard',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Agency Plan',
                'name' => 'White-label Options',
                'description' => 'White-label the platform for your clients',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Agency Plan',
                'name' => 'Client Billing',
                'description' => 'Built-in client billing and invoicing',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Agency Plan',
                'name' => 'Team Collaboration',
                'description' => 'Advanced team collaboration features',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Agency Plan',
                'name' => 'Agency Analytics',
                'description' => 'Specialized analytics for agencies',
                'status' => 'active',
            ],

            // Legacy Plan features
            [
                'plan_name' => 'Legacy Plan',
                'name' => 'Basic Features',
                'description' => 'Access to basic features only',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Legacy Plan',
                'name' => 'Limited Support',
                'description' => 'Limited email support',
                'status' => 'active',
            ],

            // Beta Plan features
            [
                'plan_name' => 'Beta Plan',
                'name' => 'Beta Features',
                'description' => 'Access to experimental beta features',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Beta Plan',
                'name' => 'Feedback Channel',
                'description' => 'Direct feedback channel to development team',
                'status' => 'active',
            ],
            [
                'plan_name' => 'Beta Plan',
                'name' => 'Early Access',
                'description' => 'Early access to new features',
                'status' => 'active',
            ],
        ];

        foreach ($planFeatures as $featureData) {
            $plan = $plans->where('name', $featureData['plan_name'])->first();
            if ($plan) {
                unset($featureData['plan_name']);
                $featureData['plan_id'] = $plan->id;
                PlanFeature::create($featureData);
            }
        }

        $this->command->info('Plan features seeded successfully!');
    }
}
