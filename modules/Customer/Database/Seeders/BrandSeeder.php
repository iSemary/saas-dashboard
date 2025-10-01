<?php

namespace Modules\Customer\Database\Seeders;

use Modules\Customer\Entities\Brand;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant and user for seeding
        $tenant = Tenant::first();
        $user = User::first();

        if (!$tenant || !$user) {
            $this->command->warn('No tenant or user found. Please run tenant and user seeders first.');
            return;
        }

        $brands = [
            [
                'name' => 'TechCorp',
                'slug' => 'techcorp',
                'description' => 'Leading technology solutions provider specializing in enterprise software and cloud services.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'GreenEnergy',
                'slug' => 'greenenergy',
                'description' => 'Sustainable energy solutions for a cleaner future. We provide solar, wind, and hydroelectric power systems.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'HealthFirst',
                'slug' => 'healthfirst',
                'description' => 'Comprehensive healthcare services with a focus on preventive care and patient wellness.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'EduTech Solutions',
                'slug' => 'edutech-solutions',
                'description' => 'Innovative educational technology platforms that enhance learning experiences for students and educators.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'FinancePro',
                'slug' => 'financepro',
                'description' => 'Professional financial advisory services helping individuals and businesses achieve their financial goals.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'RetailMax',
                'slug' => 'retailmax',
                'description' => 'Modern retail solutions with omnichannel experiences and advanced inventory management systems.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'FoodieHub',
                'slug' => 'foodiehub',
                'description' => 'Culinary excellence with farm-to-table dining experiences and premium food delivery services.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'AutoCare Plus',
                'slug' => 'autocare-plus',
                'description' => 'Complete automotive care services including maintenance, repairs, and premium car detailing.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Fashion Forward',
                'slug' => 'fashion-forward',
                'description' => 'Trendsetting fashion brand offering contemporary clothing and accessories for modern lifestyles.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
            [
                'name' => 'HomeDecor Studio',
                'slug' => 'homedecor-studio',
                'description' => 'Interior design and home decoration services creating beautiful and functional living spaces.',
                'tenant_id' => $tenant->id,
                'created_by' => $user->id,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }

        $this->command->info('Brands seeded successfully!');
    }
}
