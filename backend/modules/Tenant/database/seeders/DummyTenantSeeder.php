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
