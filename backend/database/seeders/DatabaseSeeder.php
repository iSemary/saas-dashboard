<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\tenant\RolePermissionSeeder::class,
            \Database\Seeders\tenant\SuperAdminSeeder::class,
            \Database\Seeders\tenant\SimpleUserSeeder::class,
            \Database\Seeders\tenant\PassportSetupSeeder::class,
        ]);
    }
}
