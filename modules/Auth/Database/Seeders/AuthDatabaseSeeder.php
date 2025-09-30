<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Note: Roles and permissions are handled by RolePermissionSeeder in database/seeders/Landlord/
            
            // Create users
            UserSeeder::class,
            
            // Create user metas
            UserMetaSeeder::class,
            
            // Create login attempts
            LoginAttemptSeeder::class,
        ]);
    }
}
