<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\UserMeta;

class UserMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $userMetas = [
                [
                    'user_id' => $user->id,
                    'meta_key' => 'gender',
                    'meta_value' => fake()->randomElement(['male', 'female', 'other']),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'phone',
                    'meta_value' => fake()->phoneNumber(),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'theme_mode',
                    'meta_value' => fake()->randomElement(['light', 'dark', 'auto']),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'timezone',
                    'meta_value' => fake()->randomElement(['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo', 'Australia/Sydney']),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'birthdate',
                    'meta_value' => fake()->date('Y-m-d', '2000-01-01'),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'address',
                    'meta_value' => fake()->address(),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'home_street_1',
                    'meta_value' => fake()->streetAddress(),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'home_street_2',
                    'meta_value' => fake()->secondaryAddress(),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'home_building_number',
                    'meta_value' => fake()->buildingNumber(),
                ],
                [
                    'user_id' => $user->id,
                    'meta_key' => 'home_landmark',
                    'meta_value' => fake()->randomElement(['Near Central Park', 'Opposite City Mall', 'Behind Main Station', 'Next to Hospital', 'Near School']),
                ],
            ];

            // Add currency_id for some users
            if (fake()->boolean(70)) {
                $userMetas[] = [
                    'user_id' => $user->id,
                    'meta_key' => 'currency_id',
                    'meta_value' => fake()->randomElement(['USD', 'EUR', 'GBP', 'JPY', 'CAD']),
                ];
            }

        foreach ($userMetas as $metaData) {
            UserMeta::on('landlord')->create($metaData);
        }
        }

        $this->command->info('User metas seeded successfully!');
    }
}
