<?php

namespace Modules\Customer\Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Customer\Entities\Tenant\Brand;
use Illuminate\Support\Facades\DB;

class BrandModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding brand-module relationships...');

        // Get all brands
        $brands = Brand::all();

        if ($brands->isEmpty()) {
            $this->command->warn('No brands found. Please run BrandSeeder first.');
            return;
        }

        // Get some modules from landlord database
        $modules = DB::connection('landlord')
            ->table('modules')
            ->where('status', 'active')
            ->limit(5)
            ->get();

        if ($modules->isEmpty()) {
            $this->command->warn('No modules found in landlord database.');
            return;
        }

        $this->command->info("Found {$brands->count()} brands and {$modules->count()} modules");

        // Assign modules to brands
        foreach ($brands as $brand) {
            // Assign 2-3 random modules to each brand
            $randomModules = $modules->random(rand(2, min(3, $modules->count())));

            foreach ($randomModules as $module) {
                // Use tenant database connection explicitly
                DB::connection('tenant')->table('brand_module')->updateOrInsert(
                    [
                        'brand_id' => $brand->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->command->info("Assigned {$randomModules->count()} modules to brand: {$brand->name}");
        }

        $this->command->info('✅ Brand-module relationships seeded successfully!');
    }
}
