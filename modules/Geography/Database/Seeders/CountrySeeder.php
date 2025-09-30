<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'region' => 'North America',
                'flag' => null,
                'timezone' => 'America/New_York',
                'phone_code' => '+1',
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'region' => 'North America',
                'flag' => null,
                'timezone' => 'America/Toronto',
                'phone_code' => '+1',
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/London',
                'phone_code' => '+44',
            ],
            [
                'name' => 'Germany',
                'code' => 'DE',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Berlin',
                'phone_code' => '+49',
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Paris',
                'phone_code' => '+33',
            ],
            [
                'name' => 'Italy',
                'code' => 'IT',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Rome',
                'phone_code' => '+39',
            ],
            [
                'name' => 'Spain',
                'code' => 'ES',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Madrid',
                'phone_code' => '+34',
            ],
            [
                'name' => 'Netherlands',
                'code' => 'NL',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Amsterdam',
                'phone_code' => '+31',
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'region' => 'Oceania',
                'flag' => null,
                'timezone' => 'Australia/Sydney',
                'phone_code' => '+61',
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Asia/Tokyo',
                'phone_code' => '+81',
            ],
            [
                'name' => 'China',
                'code' => 'CN',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Asia/Shanghai',
                'phone_code' => '+86',
            ],
            [
                'name' => 'India',
                'code' => 'IN',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Asia/Kolkata',
                'phone_code' => '+91',
            ],
            [
                'name' => 'Brazil',
                'code' => 'BR',
                'region' => 'South America',
                'flag' => null,
                'timezone' => 'America/Sao_Paulo',
                'phone_code' => '+55',
            ],
            [
                'name' => 'Mexico',
                'code' => 'MX',
                'region' => 'North America',
                'flag' => null,
                'timezone' => 'America/Mexico_City',
                'phone_code' => '+52',
            ],
            [
                'name' => 'South Africa',
                'code' => 'ZA',
                'region' => 'Africa',
                'flag' => null,
                'timezone' => 'Africa/Johannesburg',
                'phone_code' => '+27',
            ],
            [
                'name' => 'Egypt',
                'code' => 'EG',
                'region' => 'Africa',
                'flag' => null,
                'timezone' => 'Africa/Cairo',
                'phone_code' => '+20',
            ],
            [
                'name' => 'Turkey',
                'code' => 'TR',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Europe/Istanbul',
                'phone_code' => '+90',
            ],
            [
                'name' => 'Russia',
                'code' => 'RU',
                'region' => 'Europe',
                'flag' => null,
                'timezone' => 'Europe/Moscow',
                'phone_code' => '+7',
            ],
            [
                'name' => 'South Korea',
                'code' => 'KR',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Asia/Seoul',
                'phone_code' => '+82',
            ],
            [
                'name' => 'Singapore',
                'code' => 'SG',
                'region' => 'Asia',
                'flag' => null,
                'timezone' => 'Asia/Singapore',
                'phone_code' => '+65',
            ],
        ];

        foreach ($countries as $countryData) {
            Country::firstOrCreate(
                ['code' => $countryData['code']], 
                $countryData
            );
        }

        $this->command->info('Countries seeded successfully!');
    }
}
