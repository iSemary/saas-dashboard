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
                'plan_name' => 'Free Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 0.00,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
            [
                'plan_name' => 'Free Plan',
                'currency_code' => 'EUR',
                'country_id' => 4, // Germany
                'old_price' => 0.00,
                'new_price' => 0.00,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Starter Plan
            [
                'plan_name' => 'Starter Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 9.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
            [
                'plan_name' => 'Starter Plan',
                'currency_code' => 'EUR',
                'country_id' => 4, // Germany
                'old_price' => 0.00,
                'new_price' => 8.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Professional Plan
            [
                'plan_name' => 'Professional Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 29.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
            [
                'plan_name' => 'Professional Plan',
                'currency_code' => 'EUR',
                'country_id' => 4, // Germany
                'old_price' => 0.00,
                'new_price' => 26.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Enterprise Plan
            [
                'plan_name' => 'Enterprise Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 99.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
            [
                'plan_name' => 'Enterprise Plan',
                'currency_code' => 'EUR',
                'country_id' => 4, // Germany
                'old_price' => 0.00,
                'new_price' => 89.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Agency Plan
            [
                'plan_name' => 'Agency Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 49.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
            [
                'plan_name' => 'Agency Plan',
                'currency_code' => 'EUR',
                'country_id' => 4, // Germany
                'old_price' => 0.00,
                'new_price' => 44.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Legacy Plan - Grandfathered pricing
            [
                'plan_name' => 'Legacy Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 4.99,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],

            // Beta Plan - Free for beta testers
            [
                'plan_name' => 'Beta Plan',
                'currency_code' => 'USD',
                'country_id' => 1, // United States
                'old_price' => 0.00,
                'new_price' => 0.00,
                'change_date' => now()->toDateString(),
                'status' => 'active',
            ],
        ];

        foreach ($planPrices as $priceData) {
            $plan = $plans->where('name', $priceData['plan_name'])->first();
            $currency = $currencies->where('code', $priceData['currency_code'])->first();
            
            if ($plan && $currency) {
                unset($priceData['plan_name'], $priceData['currency_code']);
                $priceData['plan_id'] = $plan->id;
                $priceData['currency_id'] = $currency->id;
                
                PlanPrice::create($priceData);
            }
        }

        $this->command->info('Plan prices seeded successfully!');
    }
}
