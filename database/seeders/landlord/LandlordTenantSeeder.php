<?php

namespace Database\Seeders\landlord;

use Illuminate\Database\Seeder;
use Spatie\Multitenancy\Models\Tenant;

class LandlordTenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::create([
            'name' => 'landlord',
            'domain' => 'landlord',
            'database' => env("DB_DATABASE", 'landlord')
        ]);
    }
}
