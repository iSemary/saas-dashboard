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
                'description' => 'Perfect for getting started with basic features. Ideal for individuals and small projects.',
                'icon' => null,
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Starter Plan',
                'description' => 'Great for small teams and growing businesses. Includes advanced features and priority support.',
                'icon' => null,
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Professional Plan',
                'description' => 'Designed for professional teams and established businesses. Full feature access with premium support.',
                'icon' => null,
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'For large organizations with custom requirements. Includes dedicated support and custom integrations.',
                'icon' => null,
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Agency Plan',
                'description' => 'Special plan for agencies managing multiple clients. Includes white-label options and advanced analytics.',
                'icon' => null,
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Legacy Plan',
                'description' => 'Legacy plan for existing customers. Limited features but grandfathered pricing.',
                'icon' => null,
                'status' => 'inactive',
                'priority' => 0,
            ],
            [
                'name' => 'Beta Plan',
                'description' => 'Beta testing plan with experimental features. Available for select users only.',
                'icon' => null,
                'status' => 'draft',
                'priority' => 6,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('Plans seeded successfully!');
    }
}
