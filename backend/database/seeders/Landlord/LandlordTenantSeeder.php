<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Modules\Tenant\Entities\Tenant;

class LandlordTenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::updateOrCreate(
            ['name' => 'landlord'],
            [
                'domain' => 'landlord',
                'database' => env('DB_DATABASE', 'landlord')
            ]
        );
    }
}
