<?php

namespace Modules\namespace Modules\Customer\Database\Seeders\tenant;\Database\Seeders\tenant;

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
            [
                'name' => 'GreenEnergy Corp',
                'description' => 'Sustainable energy solutions and environmental consulting services.',
                'website' => 'https://greenenergy.example.com',
                'email' => 'contact@greenenergy.example.com',
                'phone' => '+1-555-0456',
                'address' => '456 Green Avenue, Portland, OR 97201',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'HealthFirst Medical',
                'description' => 'Comprehensive healthcare services and medical technology solutions.',
                'website' => 'https://healthfirst.example.com',
                'email' => 'info@healthfirst.example.com',
                'phone' => '+1-555-0789',
                'address' => '789 Health Boulevard, Boston, MA 02101',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'EduTech Academy',
                'description' => 'Educational technology and online learning platform development.',
                'website' => 'https://edutech.example.com',
                'email' => 'support@edutech.example.com',
                'phone' => '+1-555-0321',
                'address' => '321 Education Drive, Austin, TX 78701',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'FinancePro Group',
                'description' => 'Financial advisory services and investment management solutions.',
                'website' => 'https://financepro.example.com',
                'email' => 'info@financepro.example.com',
                'phone' => '+1-555-0654',
                'address' => '654 Finance Plaza, New York, NY 10001',
                'status' => 'active',
                'created_by' => 1,
            ],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::create($brandData);
            
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
            
            // Sync modules to brand
            $brand->modules()->sync($moduleIds);
        }
    }
}
