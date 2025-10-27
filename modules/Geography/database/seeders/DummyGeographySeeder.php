<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Country;
use Modules\Geography\Entities\Province;
use Modules\Geography\Entities\City;

class DummyGeographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create dummy countries
        $countries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'phone_code' => '+1',
                'region' => 'North America',
                'timezone' => 'America/New_York',
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'phone_code' => '+1',
                'region' => 'North America',
                'timezone' => 'America/Toronto',
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'phone_code' => '+44',
                'region' => 'Europe',
                'timezone' => 'Europe/London',
            ],
            [
                'name' => 'Germany',
                'code' => 'DE',
                'phone_code' => '+49',
                'region' => 'Europe',
                'timezone' => 'Europe/Berlin',
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'phone_code' => '+33',
                'region' => 'Europe',
                'timezone' => 'Europe/Paris',
            ],
        ];

        foreach ($countries as $countryData) {
            $country = Country::firstOrCreate(
                ['code' => $countryData['code']],
                $countryData
            );

            // Create dummy provinces/states for each country
            $provinces = $this->getProvincesForCountry($country->code);
            
            foreach ($provinces as $provinceData) {
                $province = Province::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $provinceData['name']
                    ],
                    array_merge($provinceData, ['country_id' => $country->id])
                );

                // Create dummy cities for each province
                $cities = $this->getCitiesForProvince($province->name);
                
                foreach ($cities as $cityData) {
                    City::firstOrCreate(
                        [
                            'province_id' => $province->id,
                            'name' => $cityData['name']
                        ],
                        array_merge($cityData, ['province_id' => $province->id])
                    );
                }
            }
        }

        $this->command->info('Dummy geography data seeded successfully!');
    }

    /**
     * Get provinces for a specific country
     */
    private function getProvincesForCountry(string $countryCode): array
    {
        $provinces = [
            'US' => [
                ['name' => 'California', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/Los_Angeles'],
                ['name' => 'New York', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/New_York'],
                ['name' => 'Texas', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/Chicago'],
                ['name' => 'Florida', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/New_York'],
            ],
            'CA' => [
                ['name' => 'Ontario', 'is_capital' => true, 'phone_code' => '+1', 'timezone' => 'America/Toronto'],
                ['name' => 'Quebec', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/Montreal'],
                ['name' => 'British Columbia', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/Vancouver'],
                ['name' => 'Alberta', 'is_capital' => false, 'phone_code' => '+1', 'timezone' => 'America/Edmonton'],
            ],
            'GB' => [
                ['name' => 'England', 'is_capital' => true, 'phone_code' => '+44', 'timezone' => 'Europe/London'],
                ['name' => 'Scotland', 'is_capital' => false, 'phone_code' => '+44', 'timezone' => 'Europe/London'],
                ['name' => 'Wales', 'is_capital' => false, 'phone_code' => '+44', 'timezone' => 'Europe/London'],
                ['name' => 'Northern Ireland', 'is_capital' => false, 'phone_code' => '+44', 'timezone' => 'Europe/London'],
            ],
            'DE' => [
                ['name' => 'Bavaria', 'is_capital' => false, 'phone_code' => '+49', 'timezone' => 'Europe/Berlin'],
                ['name' => 'North Rhine-Westphalia', 'is_capital' => false, 'phone_code' => '+49', 'timezone' => 'Europe/Berlin'],
                ['name' => 'Baden-Württemberg', 'is_capital' => false, 'phone_code' => '+49', 'timezone' => 'Europe/Berlin'],
                ['name' => 'Lower Saxony', 'is_capital' => false, 'phone_code' => '+49', 'timezone' => 'Europe/Berlin'],
            ],
            'FR' => [
                ['name' => 'Île-de-France', 'is_capital' => true, 'phone_code' => '+33', 'timezone' => 'Europe/Paris'],
                ['name' => 'Provence-Alpes-Côte d\'Azur', 'is_capital' => false, 'phone_code' => '+33', 'timezone' => 'Europe/Paris'],
                ['name' => 'Auvergne-Rhône-Alpes', 'is_capital' => false, 'phone_code' => '+33', 'timezone' => 'Europe/Paris'],
                ['name' => 'Occitanie', 'is_capital' => false, 'phone_code' => '+33', 'timezone' => 'Europe/Paris'],
            ],
        ];

        return $provinces[$countryCode] ?? [];
    }

    /**
     * Get cities for a specific province
     */
    private function getCitiesForProvince(string $provinceName): array
    {
        $cities = [
            'California' => [
                ['name' => 'Los Angeles'],
                ['name' => 'San Francisco'],
                ['name' => 'San Diego'],
            ],
            'New York' => [
                ['name' => 'New York City'],
                ['name' => 'Buffalo'],
                ['name' => 'Rochester'],
            ],
            'Texas' => [
                ['name' => 'Houston'],
                ['name' => 'Dallas'],
                ['name' => 'Austin'],
            ],
            'Florida' => [
                ['name' => 'Miami'],
                ['name' => 'Tampa'],
                ['name' => 'Orlando'],
            ],
            'Ontario' => [
                ['name' => 'Toronto'],
                ['name' => 'Ottawa'],
                ['name' => 'Hamilton'],
            ],
            'Quebec' => [
                ['name' => 'Montreal'],
                ['name' => 'Quebec City'],
                ['name' => 'Laval'],
            ],
            'England' => [
                ['name' => 'London'],
                ['name' => 'Manchester'],
                ['name' => 'Birmingham'],
            ],
            'Scotland' => [
                ['name' => 'Edinburgh'],
                ['name' => 'Glasgow'],
                ['name' => 'Aberdeen'],
            ],
            'Bavaria' => [
                ['name' => 'Munich'],
                ['name' => 'Nuremberg'],
                ['name' => 'Augsburg'],
            ],
            'Île-de-France' => [
                ['name' => 'Paris'],
                ['name' => 'Boulogne-Billancourt'],
                ['name' => 'Saint-Denis'],
            ],
        ];

        return $cities[$provinceName] ?? [];
    }
}
