<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Customer\Entities\Tenant\BrandModule;

class AddMissingModulesToBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding missing modules (HR, POS, Survey) to brand...');

        // Get the first brand (or you can specify by ID)
        $brand = Brand::first();

        if (!$brand) {
            $this->command->error('No brand found. Please create a brand first.');
            return;
        }

        $this->command->info("Processing brand: {$brand->name} (ID: {$brand->id})");

        // Get the module IDs from landlord database
        $modules = DB::connection('landlord')
            ->table('modules')
            ->whereIn('module_key', ['hr', 'pos', 'survey'])
            ->where('status', 'active')
            ->get();

        if ($modules->isEmpty()) {
            $this->command->warn('No HR, POS, or Survey modules found in landlord database. Please run ModulesSeeder first.');
            return;
        }

        $this->command->info("Found {$modules->count()} modules in landlord database");

        // Add each module to the brand if not already subscribed
        foreach ($modules as $module) {
            $existing = BrandModule::where('brand_id', $brand->id)
                ->where('module_id', $module->id)
                ->first();

            if ($existing) {
                $this->command->warn("Module {$module->module_key} is already subscribed to this brand (status: {$existing->status})");

                // If it exists but is not active, activate it
                if ($existing->status !== 'active') {
                    $existing->update(['status' => 'active']);
                    $this->command->info("Activated module {$module->module_key}");
                }
                continue;
            }

            // Create the subscription
            BrandModule::create([
                'brand_id' => $brand->id,
                'module_id' => $module->id,
                'module_key' => $module->module_key,
                'status' => 'active',
                'subscribed_at' => now(),
            ]);

            $this->command->info("✅ Subscribed brand to module: {$module->module_key} ({$module->name})");
        }

        $this->command->info('✅ Missing modules added successfully!');
    }
}
