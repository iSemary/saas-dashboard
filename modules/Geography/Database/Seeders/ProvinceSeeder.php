<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Province;
use Modules\Geography\Entities\Country;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = Country::all();

        if ($countries->isEmpty()) {
            $this->command->warn('No countries found. Skipping province seeding.');
            return;
        }

        $provinces = [
            // United States
            [
                'name' => 'California',
                'country_name' => 'United States',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Los_Angeles',
                'phone_code' => '+1',
            ],
            [
                'name' => 'New York',
                'country_name' => 'United States',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/New_York',
                'phone_code' => '+1',
            ],
            [
                'name' => 'Texas',
                'country_name' => 'United States',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Chicago',
                'phone_code' => '+1',
            ],
            [
                'name' => 'Florida',
                'country_name' => 'United States',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/New_York',
                'phone_code' => '+1',
            ],
            [
                'name' => 'Washington',
                'country_name' => 'United States',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Los_Angeles',
                'phone_code' => '+1',
            ],

            // Canada
            [
                'name' => 'Ontario',
                'country_name' => 'Canada',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Toronto',
                'phone_code' => '+1',
            ],
            [
                'name' => 'Quebec',
                'country_name' => 'Canada',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Montreal',
                'phone_code' => '+1',
            ],
            [
                'name' => 'British Columbia',
                'country_name' => 'Canada',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Vancouver',
                'phone_code' => '+1',
            ],

            // United Kingdom
            [
                'name' => 'England',
                'country_name' => 'United Kingdom',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
            ],
            [
                'name' => 'Scotland',
                'country_name' => 'United Kingdom',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
            ],
            [
                'name' => 'Wales',
                'country_name' => 'United Kingdom',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
            ],

            // Germany
            [
                'name' => 'Bavaria',
                'country_name' => 'Germany',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Berlin',
                'phone_code' => '+49',
            ],
            [
                'name' => 'North Rhine-Westphalia',
                'country_name' => 'Germany',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Berlin',
                'phone_code' => '+49',
            ],
            [
                'name' => 'Baden-Württemberg',
                'country_name' => 'Germany',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Berlin',
                'phone_code' => '+49',
            ],

            // France
            [
                'name' => 'Île-de-France',
                'country_name' => 'France',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Paris',
                'phone_code' => '+33',
            ],
            [
                'name' => 'Provence-Alpes-Côte d\'Azur',
                'country_name' => 'France',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Paris',
                'phone_code' => '+33',
            ],

            // Italy
            [
                'name' => 'Lazio',
                'country_name' => 'Italy',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Rome',
                'phone_code' => '+39',
            ],
            [
                'name' => 'Lombardy',
                'country_name' => 'Italy',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Rome',
                'phone_code' => '+39',
            ],

            // Spain
            [
                'name' => 'Madrid',
                'country_name' => 'Spain',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Madrid',
                'phone_code' => '+34',
            ],
            [
                'name' => 'Catalonia',
                'country_name' => 'Spain',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Madrid',
                'phone_code' => '+34',
            ],

            // Netherlands
            [
                'name' => 'North Holland',
                'country_name' => 'Netherlands',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Amsterdam',
                'phone_code' => '+31',
            ],
            [
                'name' => 'South Holland',
                'country_name' => 'Netherlands',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Amsterdam',
                'phone_code' => '+31',
            ],

            // Australia
            [
                'name' => 'New South Wales',
                'country_name' => 'Australia',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Australia/Sydney',
                'phone_code' => '+61',
            ],
            [
                'name' => 'Victoria',
                'country_name' => 'Australia',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Australia/Melbourne',
                'phone_code' => '+61',
            ],

            // Japan
            [
                'name' => 'Tokyo',
                'country_name' => 'Japan',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Asia/Tokyo',
                'phone_code' => '+81',
            ],
            [
                'name' => 'Osaka',
                'country_name' => 'Japan',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Asia/Tokyo',
                'phone_code' => '+81',
            ],

            // China
            [
                'name' => 'Beijing',
                'country_name' => 'China',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Asia/Shanghai',
                'phone_code' => '+86',
            ],
            [
                'name' => 'Shanghai',
                'country_name' => 'China',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Asia/Shanghai',
                'phone_code' => '+86',
            ],

            // India
            [
                'name' => 'Delhi',
                'country_name' => 'India',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Asia/Kolkata',
                'phone_code' => '+91',
            ],
            [
                'name' => 'Maharashtra',
                'country_name' => 'India',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Asia/Kolkata',
                'phone_code' => '+91',
            ],

            // Brazil
            [
                'name' => 'São Paulo',
                'country_name' => 'Brazil',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Sao_Paulo',
                'phone_code' => '+55',
            ],
            [
                'name' => 'Rio de Janeiro',
                'country_name' => 'Brazil',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Sao_Paulo',
                'phone_code' => '+55',
            ],

            // Mexico
            [
                'name' => 'Mexico City',
                'country_name' => 'Mexico',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'America/Mexico_City',
                'phone_code' => '+52',
            ],
            [
                'name' => 'Jalisco',
                'country_name' => 'Mexico',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'America/Mexico_City',
                'phone_code' => '+52',
            ],

            // South Africa
            [
                'name' => 'Gauteng',
                'country_name' => 'South Africa',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Africa/Johannesburg',
                'phone_code' => '+27',
            ],
            [
                'name' => 'Western Cape',
                'country_name' => 'South Africa',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Africa/Johannesburg',
                'phone_code' => '+27',
            ],

            // Egypt
            [
                'name' => 'Cairo',
                'country_name' => 'Egypt',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Africa/Cairo',
                'phone_code' => '+20',
            ],
            [
                'name' => 'Alexandria',
                'country_name' => 'Egypt',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Africa/Cairo',
                'phone_code' => '+20',
            ],

            // Turkey
            [
                'name' => 'Istanbul',
                'country_name' => 'Turkey',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Istanbul',
                'phone_code' => '+90',
            ],
            [
                'name' => 'Ankara',
                'country_name' => 'Turkey',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Istanbul',
                'phone_code' => '+90',
            ],

            // Russia
            [
                'name' => 'Moscow',
                'country_name' => 'Russia',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Europe/Moscow',
                'phone_code' => '+7',
            ],
            [
                'name' => 'Saint Petersburg',
                'country_name' => 'Russia',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Europe/Moscow',
                'phone_code' => '+7',
            ],

            // South Korea
            [
                'name' => 'Seoul',
                'country_name' => 'South Korea',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Asia/Seoul',
                'phone_code' => '+82',
            ],
            [
                'name' => 'Busan',
                'country_name' => 'South Korea',
                'is_capital' => false,
                'flag' => null,
                'timezone' => 'Asia/Seoul',
                'phone_code' => '+82',
            ],

            // Singapore
            [
                'name' => 'Singapore',
                'country_name' => 'Singapore',
                'is_capital' => true,
                'flag' => null,
                'timezone' => 'Asia/Singapore',
                'phone_code' => '+65',
            ],
        ];

        foreach ($provinces as $provinceData) {
            $country = $countries->where('name', $provinceData['country_name'])->first();
            if ($country) {
                unset($provinceData['country_name']);
                $provinceData['country_id'] = $country->id;
                Province::firstOrCreate(
                    ['name' => $provinceData['name'], 'country_id' => $provinceData['country_id']], 
                    $provinceData
                );
            }
        }

        $this->command->info('Provinces seeded successfully!');
    }
}
