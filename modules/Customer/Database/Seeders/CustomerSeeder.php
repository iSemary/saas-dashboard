<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Customer\Entities\Customer;
use Modules\Utilities\Entities\Category;
use Modules\Tenant\Entities\Tenant;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some categories and tenants for relationships
        $categories = Category::all();
        $tenants = Tenant::all();

        if ($categories->isEmpty() || $tenants->isEmpty()) {
            $this->command->warn('No categories or tenants found. Skipping customer seeding.');
            return;
        }

        $customers = [
            [
                'name' => 'Acme Corporation',
                'username' => 'acme_corp',
                'tenant_id' => $tenants->first()->id,
                'category_id' => $categories->where('slug', 'business')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'TechStart Solutions',
                'username' => 'techstart_solutions',
                'tenant_id' => $tenants->skip(1)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'technology')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Global Industries Ltd',
                'username' => 'global_industries',
                'tenant_id' => $tenants->skip(2)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'manufacturing')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Digital Marketing Pro',
                'username' => 'digital_marketing_pro',
                'tenant_id' => $tenants->skip(3)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'marketing')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Healthcare Partners',
                'username' => 'healthcare_partners',
                'tenant_id' => $tenants->skip(4)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'healthcare')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'EduTech Innovations',
                'username' => 'edutech_innovations',
                'tenant_id' => $tenants->skip(5)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'education')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Finance First',
                'username' => 'finance_first',
                'tenant_id' => $tenants->skip(6)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'finance')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Retail Masters',
                'username' => 'retail_masters',
                'tenant_id' => $tenants->skip(7)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'retail')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Real Estate Group',
                'username' => 'real_estate_group',
                'tenant_id' => $tenants->skip(8)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'real-estate')->first()?->id ?? $categories->first()->id,
            ],
            [
                'name' => 'Transport Solutions',
                'username' => 'transport_solutions',
                'tenant_id' => $tenants->skip(9)->first()?->id ?? $tenants->first()->id,
                'category_id' => $categories->where('slug', 'transportation')->first()?->id ?? $categories->first()->id,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('Customers seeded successfully!');
    }
}
