<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Currency;
use Modules\Utilities\Entities\Module;

class ModulePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usdCurrency = Currency::where('code', 'USD')->first();

        if (!$usdCurrency) {
            $this->command->warn('USD currency not found. Run CurrencySeeder first.');
            return;
        }

        // Active module pricing configuration
        $modulePricing = [
            // Core modules - add-ons
            'crm' => [
                'base_price' => 49.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'hr' => [
                'base_price' => 39.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'pos' => [
                'base_price' => 29.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'survey' => [
                'base_price' => 19.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            // Inactive modules - set as addons but no price
            'inventory' => [
                'base_price' => 25.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'accounting' => [
                'base_price' => 59.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'project_management' => [
                'base_price' => 35.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
            'expenses' => [
                'base_price' => 15.00,
                'currency_id' => $usdCurrency->id,
                'billing_cycle' => 'monthly',
                'is_addon' => true,
                'trial_days' => 14,
            ],
        ];

        foreach ($modulePricing as $moduleKey => $pricing) {
            $module = Module::where('module_key', $moduleKey)->first();
            
            if ($module) {
                $module->update($pricing);
                $this->command->info("Updated pricing for module: {$moduleKey}");
            } else {
                $this->command->warn("Module not found: {$moduleKey}");
            }
        }
    }
}
