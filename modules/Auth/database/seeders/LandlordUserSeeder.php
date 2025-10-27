<?php

namespace Modules\Auth\Database\Seeders;


use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;

class LandlordUserSeeder extends Seeder
{

    public function run(): void
    {
        $userInstance = new User();
        $userInstance->setConnection('landlord');

        $user = $userInstance->updateOrCreate(
            ['email' => env("DEFAULT_LANDLORD_EMAIL")],
            [
                'name' => env("DEFAULT_LANDLORD_NAME"),
                'username' => env("DEFAULT_LANDLORD_USERNAME"),
                'country_id' => '1',
                'language_id' => '1',
                'factor_authenticate' => 0,
                'google2fa_secret' => null,
                'password' => bcrypt(env("DEFAULT_LANDLORD_PASSWORD")),
            ]
        );

        // Get the landlord role
        $landlordRole = Role::where('name', 'landlord')->first();

        // Assign the role to the user if the role exists
        if ($landlordRole) {
            $user->assignRole($landlordRole);
        }
    }
}
