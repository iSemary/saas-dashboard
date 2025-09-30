<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\City;
use Modules\Geography\Entities\Province;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = Province::all();

        if ($provinces->isEmpty()) {
            $this->command->warn('No provinces found. Skipping city seeding.');
            return;
        }

        $cities = [
            // United States - California
            [
                'name' => 'Los Angeles',
                'province_name' => 'California',
            ],
            [
                'name' => 'San Francisco',
                'province_name' => 'California',
            ],
            [
                'name' => 'San Diego',
                'province_name' => 'California',
            ],
            [
                'name' => 'Sacramento',
                'province_name' => 'California',
            ],

            // United States - New York
            [
                'name' => 'New York City',
                'province_name' => 'New York',
            ],
            [
                'name' => 'Buffalo',
                'province_name' => 'New York',
            ],
            [
                'name' => 'Rochester',
                'province_name' => 'New York',
            ],

            // United States - Texas
            [
                'name' => 'Houston',
                'province_name' => 'Texas',
            ],
            [
                'name' => 'Dallas',
                'province_name' => 'Texas',
            ],
            [
                'name' => 'Austin',
                'province_name' => 'Texas',
            ],

            // United States - Florida
            [
                'name' => 'Miami',
                'province_name' => 'Florida',
            ],
            [
                'name' => 'Orlando',
                'province_name' => 'Florida',
            ],
            [
                'name' => 'Tampa',
                'province_name' => 'Florida',
            ],

            // United States - Washington
            [
                'name' => 'Seattle',
                'province_name' => 'Washington',
            ],
            [
                'name' => 'Spokane',
                'province_name' => 'Washington',
            ],

            // Canada - Ontario
            [
                'name' => 'Toronto',
                'province_name' => 'Ontario',
            ],
            [
                'name' => 'Ottawa',
                'province_name' => 'Ontario',
            ],
            [
                'name' => 'Hamilton',
                'province_name' => 'Ontario',
            ],

            // Canada - Quebec
            [
                'name' => 'Montreal',
                'province_name' => 'Quebec',
            ],
            [
                'name' => 'Quebec City',
                'province_name' => 'Quebec',
            ],

            // Canada - British Columbia
            [
                'name' => 'Vancouver',
                'province_name' => 'British Columbia',
            ],
            [
                'name' => 'Victoria',
                'province_name' => 'British Columbia',
            ],

            // United Kingdom - England
            [
                'name' => 'London',
                'province_name' => 'England',
            ],
            [
                'name' => 'Manchester',
                'province_name' => 'England',
            ],
            [
                'name' => 'Birmingham',
                'province_name' => 'England',
            ],

            // United Kingdom - Scotland
            [
                'name' => 'Edinburgh',
                'province_name' => 'Scotland',
            ],
            [
                'name' => 'Glasgow',
                'province_name' => 'Scotland',
            ],

            // United Kingdom - Wales
            [
                'name' => 'Cardiff',
                'province_name' => 'Wales',
            ],

            // Germany - Bavaria
            [
                'name' => 'Munich',
                'province_name' => 'Bavaria',
            ],
            [
                'name' => 'Nuremberg',
                'province_name' => 'Bavaria',
            ],

            // Germany - North Rhine-Westphalia
            [
                'name' => 'Cologne',
                'province_name' => 'North Rhine-Westphalia',
            ],
            [
                'name' => 'Düsseldorf',
                'province_name' => 'North Rhine-Westphalia',
            ],

            // Germany - Baden-Württemberg
            [
                'name' => 'Stuttgart',
                'province_name' => 'Baden-Württemberg',
            ],

            // France - Île-de-France
            [
                'name' => 'Paris',
                'province_name' => 'Île-de-France',
            ],

            // France - Provence-Alpes-Côte d'Azur
            [
                'name' => 'Marseille',
                'province_name' => 'Provence-Alpes-Côte d\'Azur',
            ],
            [
                'name' => 'Nice',
                'province_name' => 'Provence-Alpes-Côte d\'Azur',
            ],

            // Italy - Lazio
            [
                'name' => 'Rome',
                'province_name' => 'Lazio',
            ],

            // Italy - Lombardy
            [
                'name' => 'Milan',
                'province_name' => 'Lombardy',
            ],

            // Spain - Madrid
            [
                'name' => 'Madrid',
                'province_name' => 'Madrid',
            ],

            // Spain - Catalonia
            [
                'name' => 'Barcelona',
                'province_name' => 'Catalonia',
            ],

            // Netherlands - North Holland
            [
                'name' => 'Amsterdam',
                'province_name' => 'North Holland',
            ],

            // Netherlands - South Holland
            [
                'name' => 'Rotterdam',
                'province_name' => 'South Holland',
            ],

            // Australia - New South Wales
            [
                'name' => 'Sydney',
                'province_name' => 'New South Wales',
            ],

            // Australia - Victoria
            [
                'name' => 'Melbourne',
                'province_name' => 'Victoria',
            ],

            // Japan - Tokyo
            [
                'name' => 'Tokyo',
                'province_name' => 'Tokyo',
            ],

            // Japan - Osaka
            [
                'name' => 'Osaka',
                'province_name' => 'Osaka',
            ],

            // China - Beijing
            [
                'name' => 'Beijing',
                'province_name' => 'Beijing',
            ],

            // China - Shanghai
            [
                'name' => 'Shanghai',
                'province_name' => 'Shanghai',
            ],

            // India - Delhi
            [
                'name' => 'New Delhi',
                'province_name' => 'Delhi',
            ],

            // India - Maharashtra
            [
                'name' => 'Mumbai',
                'province_name' => 'Maharashtra',
            ],

            // Brazil - São Paulo
            [
                'name' => 'São Paulo',
                'province_name' => 'São Paulo',
            ],

            // Brazil - Rio de Janeiro
            [
                'name' => 'Rio de Janeiro',
                'province_name' => 'Rio de Janeiro',
            ],

            // Mexico - Mexico City
            [
                'name' => 'Mexico City',
                'province_name' => 'Mexico City',
            ],

            // Mexico - Jalisco
            [
                'name' => 'Guadalajara',
                'province_name' => 'Jalisco',
            ],

            // South Africa - Gauteng
            [
                'name' => 'Johannesburg',
                'province_name' => 'Gauteng',
            ],

            // South Africa - Western Cape
            [
                'name' => 'Cape Town',
                'province_name' => 'Western Cape',
            ],

            // Egypt - Cairo
            [
                'name' => 'Cairo',
                'province_name' => 'Cairo',
            ],

            // Egypt - Alexandria
            [
                'name' => 'Alexandria',
                'province_name' => 'Alexandria',
            ],

            // Turkey - Istanbul
            [
                'name' => 'Istanbul',
                'province_name' => 'Istanbul',
            ],

            // Turkey - Ankara
            [
                'name' => 'Ankara',
                'province_name' => 'Ankara',
            ],

            // Russia - Moscow
            [
                'name' => 'Moscow',
                'province_name' => 'Moscow',
            ],

            // Russia - Saint Petersburg
            [
                'name' => 'Saint Petersburg',
                'province_name' => 'Saint Petersburg',
            ],

            // South Korea - Seoul
            [
                'name' => 'Seoul',
                'province_name' => 'Seoul',
            ],

            // South Korea - Busan
            [
                'name' => 'Busan',
                'province_name' => 'Busan',
            ],

            // Singapore
            [
                'name' => 'Singapore',
                'province_name' => 'Singapore',
            ],
        ];

        foreach ($cities as $cityData) {
            $province = $provinces->where('name', $cityData['province_name'])->first();
            if ($province) {
                unset($cityData['province_name']);
                $cityData['province_id'] = $province->id;
                City::create($cityData);
            }
        }

        $this->command->info('Cities seeded successfully!');
    }
}
