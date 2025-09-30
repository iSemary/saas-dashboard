<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;

class SubscriptionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            PlanFeatureSeeder::class,
            PlanPriceSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
