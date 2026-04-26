<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Entities\PlanPrice;
use Modules\Subscription\Entities\Plan;
use Modules\Utilities\Entities\Currency;

class PlanPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = Plan::all();
        $currencies = Currency::all();

        if ($plans->isEmpty() || $currencies->isEmpty()) {
            $this->command->warn('No plans or currencies found. Skipping plan price seeding.');
            return;
        }

        $planPrices = [
            // Free Plan - Always free
            [
                'plan_slug' => 'free-plan',
                'currency_code' => 'USD',
                'country_code' => null, // Global pricing
                'price' => 0.00,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'plan_slug' => 'free-plan',
                'currency_code' => 'EUR',
                'country_code' => null, // Global pricing
                'price' => 0.00,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],

            // Starter Plan
            [
                'plan_slug' => 'starter-plan',
                'currency_code' => 'USD',
                'country_code' => null, // Global pricing
                'price' => 9.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'plan_slug' => 'starter-plan',
                'currency_code' => 'EUR',
                'country_code' => null, // Global pricing
                'price' => 8.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],

            // Professional Plan
            [
                'plan_slug' => 'professional-plan',
                'currency_code' => 'USD',
                'country_code' => null, // Global pricing
                'price' => 29.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'plan_slug' => 'professional-plan',
                'currency_code' => 'EUR',
                'country_code' => null, // Global pricing
                'price' => 26.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],

            // Enterprise Plan
            [
                'plan_slug' => 'enterprise-plan',
                'currency_code' => 'USD',
                'country_code' => null, // Global pricing
                'price' => 99.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],
            [
                'plan_slug' => 'enterprise-plan',
                'currency_code' => 'EUR',
                'country_code' => null, // Global pricing
                'price' => 89.99,
                'setup_fee' => 0.00,
                'billing_cycle' => 'monthly',
                'billing_interval' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'metadata' => null,
                'status' => 'active',
            ],
        ];

        foreach ($planPrices as $priceData) {
            $plan = $plans->where('slug', $priceData['plan_slug'])->first();
            $currency = $currencies->where('code', $priceData['currency_code'])->first();

            if ($plan && $currency) {
                unset($priceData['plan_slug'], $priceData['currency_code']);
                $priceData['plan_id'] = $plan->id;
                $priceData['currency_id'] = $currency->id;

                PlanPrice::create($priceData);
            }
        }

        $this->command->info('Plan prices seeded successfully!');
    }
}
