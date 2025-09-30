<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Geography\Entities\Country;
use Modules\Localization\Entities\Language;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get default country and language
        $defaultCountry = Country::first();
        $defaultLanguage = Language::first();

        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@example.com',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'username' => 'manager',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Editor User',
                'email' => 'editor@example.com',
                'username' => 'editor',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'username' => 'user',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'username' => 'customer',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Support User',
                'email' => 'support@example.com',
                'username' => 'support',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Developer User',
                'email' => 'developer@example.com',
                'username' => 'developer',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Moderator User',
                'email' => 'moderator@example.com',
                'username' => 'moderator',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'username' => 'guest',
                'password' => Hash::make('password123'),
                'country_id' => $defaultCountry?->id,
                'language_id' => $defaultLanguage?->id ?? 1,
                'factor_authenticate' => 0,
                'email_verified_at' => null, // Guest user not verified
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $createdUsers[] = $user;
        }

        // Assign roles to users
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $editorRole = Role::where('name', 'Editor')->first();
        $userRole = Role::where('name', 'User')->first();
        $customerRole = Role::where('name', 'Customer')->first();
        $supportRole = Role::where('name', 'Support')->first();
        $developerRole = Role::where('name', 'Developer')->first();
        $moderatorRole = Role::where('name', 'Moderator')->first();
        $guestRole = Role::where('name', 'Guest')->first();

        // Assign roles
        if ($createdUsers[0] && $superAdminRole) {
            $createdUsers[0]->assignRole($superAdminRole);
        }
        if ($createdUsers[1] && $adminRole) {
            $createdUsers[1]->assignRole($adminRole);
        }
        if ($createdUsers[2] && $managerRole) {
            $createdUsers[2]->assignRole($managerRole);
        }
        if ($createdUsers[3] && $editorRole) {
            $createdUsers[3]->assignRole($editorRole);
        }
        if ($createdUsers[4] && $userRole) {
            $createdUsers[4]->assignRole($userRole);
        }
        if ($createdUsers[5] && $customerRole) {
            $createdUsers[5]->assignRole($customerRole);
        }
        if ($createdUsers[6] && $supportRole) {
            $createdUsers[6]->assignRole($supportRole);
        }
        if ($createdUsers[7] && $developerRole) {
            $createdUsers[7]->assignRole($developerRole);
        }
        if ($createdUsers[8] && $moderatorRole) {
            $createdUsers[8]->assignRole($moderatorRole);
        }
        if ($createdUsers[9] && $guestRole) {
            $createdUsers[9]->assignRole($guestRole);
        }

        $this->command->info('Users seeded successfully!');
    }
}
