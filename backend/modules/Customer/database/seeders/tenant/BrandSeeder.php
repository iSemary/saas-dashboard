<?php

namespace Modules\Customer\Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Utilities\Entities\Module;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample brands
        $brands = [
            [
                'name' => 'TechCorp Solutions',
                'description' => 'Leading technology solutions provider specializing in enterprise software and digital transformation.',
                'website' => 'https://techcorp.example.com',
                'email' => 'info@techcorp.example.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Silicon Valley, CA 94000',
                'status' => 'active',
                'created_by' => 1,
            ],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::updateOrCreate(
                ['name' => $brandData['name']],
                $brandData
            );

            // Assign random modules to each brand
            $this->assignModulesToBrand($brand);
        }
    }

    /**
     * Assign modules to a brand
     */
    private function assignModulesToBrand(Brand $brand): void
    {
        // Get available modules from landlord database
        $modules = Module::where('status', 'active')->get();

        if ($modules->count() > 0) {
            // Assign 2-4 random modules to each brand
            $randomModules = $modules->random(rand(2, min(4, $modules->count())));

            $moduleIds = $randomModules->pluck('id')->toArray();

            // Sync modules using the tenant pivot table directly.
            // Brand::modules() returns a collection by design, not a relation builder.
            DB::table('brand_module')->where('brand_id', $brand->id)->delete();

            $rows = array_map(fn ($moduleId) => [
                'brand_id' => $brand->id,
                'module_id' => $moduleId,
            ], $moduleIds);

            DB::table('brand_module')->insert($rows);
        }
    }
}
