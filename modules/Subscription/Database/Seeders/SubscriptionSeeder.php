<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\Plan;
use Modules\Utilities\Entities\Currency;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = Plan::all();
        $currencies = Currency::all();
        $users = User::all();
        $tenants = Tenant::all();

        if ($plans->isEmpty() || $currencies->isEmpty() || $users->isEmpty() || $tenants->isEmpty()) {
            $this->command->warn('Missing required data. Skipping subscription seeding.');
            return;
        }

        $subscriptions = [
            [
                'tenant_id' => $tenants->first()->id,
                'user_id' => $users->first()->id,
                'plan_name' => 'Free Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => null, // Free plan has no end date
                'status' => 'active',
                'price' => 0.00,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 1 ? $tenants->skip(1)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 1 ? $users->skip(1)->first()->id : $users->first()->id,
                'plan_name' => 'Starter Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
                'price' => 9.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 2 ? $tenants->skip(2)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 2 ? $users->skip(2)->first()->id : $users->first()->id,
                'plan_name' => 'Professional Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->addDays(305), // Yearly subscription
                'status' => 'active',
                'price' => 299.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 3 ? $tenants->skip(3)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 3 ? $users->skip(3)->first()->id : $users->first()->id,
                'plan_name' => 'Enterprise Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->addDays(275), // Yearly subscription
                'status' => 'active',
                'price' => 999.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 4 ? $tenants->skip(4)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 4 ? $users->skip(4)->first()->id : $users->first()->id,
                'plan_name' => 'Agency Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(45),
                'end_date' => Carbon::now()->addDays(320), // Yearly subscription
                'status' => 'active',
                'price' => 499.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 5 ? $tenants->skip(5)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 5 ? $users->skip(5)->first()->id : $users->first()->id,
                'plan_name' => 'Starter Plan',
                'currency_code' => 'EUR',
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
                'price' => 8.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 6 ? $tenants->skip(6)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 6 ? $users->skip(6)->first()->id : $users->first()->id,
                'plan_name' => 'Professional Plan',
                'currency_code' => 'EUR',
                'start_date' => Carbon::now()->subDays(120),
                'end_date' => Carbon::now()->subDays(1), // Expired
                'status' => 'expired',
                'price' => 269.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 7 ? $tenants->skip(7)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 7 ? $users->skip(7)->first()->id : $users->first()->id,
                'plan_name' => 'Starter Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(180),
                'end_date' => Carbon::now()->addDays(185), // Yearly subscription
                'status' => 'canceled',
                'price' => 99.99,
                'canceled_at' => Carbon::now()->subDays(5),
                'cancellation_reason' => 'Switching to a different plan',
            ],
            [
                'tenant_id' => $tenants->count() > 8 ? $tenants->skip(8)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 8 ? $users->skip(8)->first()->id : $users->first()->id,
                'plan_name' => 'Legacy Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(365),
                'end_date' => null, // Legacy plan has no end date
                'status' => 'active',
                'price' => 4.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 9 ? $tenants->skip(9)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 9 ? $users->skip(9)->first()->id : $users->first()->id,
                'plan_name' => 'Beta Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => null, // Beta plan has no end date
                'status' => 'active',
                'price' => 0.00,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
            [
                'tenant_id' => $tenants->count() > 10 ? $tenants->skip(10)->first()->id : $tenants->first()->id,
                'user_id' => $users->count() > 10 ? $users->skip(10)->first()->id : $users->first()->id,
                'plan_name' => 'Professional Plan',
                'currency_code' => 'USD',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'status' => 'pending',
                'price' => 29.99,
                'canceled_at' => null,
                'cancellation_reason' => null,
            ],
        ];

        foreach ($subscriptions as $subscriptionData) {
            $plan = $plans->where('name', $subscriptionData['plan_name'])->first();
            $currency = $currencies->where('code', $subscriptionData['currency_code'])->first();
            
            if ($plan && $currency) {
                unset($subscriptionData['plan_name'], $subscriptionData['currency_code']);
                $subscriptionData['plan_id'] = $plan->id;
                $subscriptionData['currency_id'] = $currency->id;
                
                Subscription::create($subscriptionData);
            }
        }

        $this->command->info('Subscriptions seeded successfully!');
    }
}
