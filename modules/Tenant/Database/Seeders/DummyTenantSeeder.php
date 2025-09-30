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
                'database' => 'tenant_techcorp_' . time(),
            ],
            [
                'name' => 'HealthCare Plus',
                'domain' => 'healthcare',
                'database' => 'tenant_healthcare_' . time(),
            ],
            [
                'name' => 'EduLearn Academy',
                'domain' => 'edulearn',
                'database' => 'tenant_edulearn_' . time(),
            ],
            [
                'name' => 'RetailMax Store',
                'domain' => 'retailmax',
                'database' => 'tenant_retailmax_' . time(),
            ],
            [
                'name' => 'FinancePro Services',
                'domain' => 'financepro',
                'database' => 'tenant_financepro_' . time(),
            ],
            [
                'name' => 'ManufacturingCo',
                'domain' => 'manufacturing',
                'database' => 'tenant_manufacturing_' . time(),
            ],
            [
                'name' => 'RealEstate Pro',
                'domain' => 'realestate',
                'database' => 'tenant_realestate_' . time(),
            ],
            [
                'name' => 'TransportLogistics',
                'domain' => 'transport',
                'database' => 'tenant_transport_' . time(),
            ],
            [
                'name' => 'EnergySolutions',
                'domain' => 'energy',
                'database' => 'tenant_energy_' . time(),
            ],
            [
                'name' => 'MediaEntertainment',
                'domain' => 'media',
                'database' => 'tenant_media_' . time(),
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
