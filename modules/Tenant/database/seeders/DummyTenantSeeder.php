<?php

namespace Modules\Tenant\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tenant\Entities\Tenant;

class DummyTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyTenants = [
            [
                'name' => 'TechCorp Solutions',
                'domain' => 'techcorp',
                'database' => 'saas_techcorp',
            ],
            [
                'name' => 'HealthCare Plus',
                'domain' => 'healthcare',
                'database' => 'saas_healthcare',
            ],
            [
                'name' => 'EduLearn Academy',
                'domain' => 'edulearn',
                'database' => 'saas_edulearn',
            ],
            [
                'name' => 'RetailMax Store',
                'domain' => 'retailmax',
                'database' => 'saas_retailmax',
            ],
            [
                'name' => 'FinancePro Services',
                'domain' => 'financepro',
                'database' => 'saas_financepro',
            ],
            [
                'name' => 'ManufacturingCo',
                'domain' => 'manufacturing',
                'database' => 'saas_manufacturing',
            ],
            [
                'name' => 'RealEstate Pro',
                'domain' => 'realestate',
                'database' => 'saas_realestate',
            ],
            [
                'name' => 'TransportLogistics',
                'domain' => 'transport',
                'database' => 'saas_transport',
            ],
            [
                'name' => 'EnergySolutions',
                'domain' => 'energy',
                'database' => 'saas_energy',
            ],
            [
                'name' => 'MediaEntertainment',
                'domain' => 'media',
                'database' => 'saas_media',
            ],
        ];

        foreach ($dummyTenants as $tenantData) {
            Tenant::firstOrCreate(
                ['domain' => $tenantData['domain']],
                $tenantData
            );
        }

        $this->command->info('Dummy tenants seeded successfully!');
    }
}
