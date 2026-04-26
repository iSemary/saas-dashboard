<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Entities\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free-plan',
                'description' => 'Perfect for getting started with basic features. Ideal for individuals and small projects.',
                'features_summary' => '<ul><li>5 Projects</li><li>1GB Storage</li><li>Email Support</li><li>Basic Dashboard</li></ul>',
                'price' => 0.00,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sort_order' => 1,
                'is_popular' => false,
                'is_custom' => false,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Starter Plan',
                'slug' => 'starter-plan',
                'description' => 'Great for small teams and growing businesses. Includes advanced features and priority support.',
                'features_summary' => '<ul><li>25 Projects</li><li>10GB Storage</li><li>Priority Support</li><li>API Access</li><li>Advanced Analytics</li></ul>',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sort_order' => 2,
                'is_popular' => true,
                'is_custom' => false,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Professional Plan',
                'slug' => 'professional-plan',
                'description' => 'Designed for professional teams and established businesses. Full feature access with premium support.',
                'features_summary' => '<ul><li>Unlimited Projects</li><li>100GB Storage</li><li>Live Chat Support</li><li>Full API Access</li><li>Custom Integrations</li><li>Advanced Analytics</li></ul>',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sort_order' => 3,
                'is_popular' => false,
                'is_custom' => false,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise-plan',
                'description' => 'For large organizations with custom requirements. Includes dedicated support and custom integrations.',
                'features_summary' => '<ul><li>Unlimited Everything</li><li>Dedicated Support</li><li>Custom Development</li><li>SLA Guarantee</li><li>On-premise Option</li><li>White-label Options</li></ul>',
                'price' => 99.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'sort_order' => 4,
                'is_popular' => false,
                'is_custom' => true,
                'metadata' => null,
                'status' => 'active',
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('Plans seeded successfully!');
    }
}
