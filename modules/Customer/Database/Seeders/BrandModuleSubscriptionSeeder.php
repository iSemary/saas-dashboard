<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\BrandModuleSubscription;

class BrandModuleSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all brands
        $brands = Brand::all();
        
        if ($brands->isEmpty()) 
        {
            $this->command->info('No brands found. Please create brands first.');
            return;
        }

        $modules = [
            'crm' => 'CRM',
            'hr' => 'Human Resources',
            'accounting' => 'Accounting',
            'sales' => 'Sales',
            'inventory' => 'Inventory',
            'reporting' => 'Reporting',
        ];

        foreach ($brands as $brand) 
        {
            // Randomly assign modules to each brand (2-4 modules per brand)
            $moduleKeys = array_rand($modules, rand(2, min(4, count($modules))));
            if (!is_array($moduleKeys)) 
            {
                $moduleKeys = [$moduleKeys];
            }

            foreach ($moduleKeys as $moduleKey) 
            {
                BrandModuleSubscription::create([
                    'brand_id' => $brand->id,
                    'module_key' => $moduleKey,
                    'module_name' => $modules[$moduleKey],
                    'subscription_status' => 'active',
                    'subscription_start' => now()->subDays(rand(30, 180)),
                    'subscription_end' => null,
                    'module_config' => [
                        'auto_provision' => true,
                        'notifications_enabled' => true,
                    ],
                    'created_by' => 1, // Assuming admin user ID is 1
                ]);
            }
        }

        $totalSubscriptions = BrandModuleSubscription::count();
        $this->command->info("Created {$totalSubscriptions} brand module subscriptions successfully!");
    }
}
