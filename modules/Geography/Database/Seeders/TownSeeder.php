<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Town;
use Modules\Geography\Entities\City;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();

        if ($cities->isEmpty()) {
            $this->command->warn('No cities found. Skipping town seeding.');
            return;
        }

        $towns = [
            // Los Angeles area
            [
                'name' => 'Beverly Hills',
                'city_name' => 'Los Angeles',
            ],
            [
                'name' => 'Santa Monica',
                'city_name' => 'Los Angeles',
            ],
            [
                'name' => 'Hollywood',
                'city_name' => 'Los Angeles',
            ],

            // New York City area
            [
                'name' => 'Manhattan',
                'city_name' => 'New York City',
            ],
            [
                'name' => 'Brooklyn',
                'city_name' => 'New York City',
            ],
            [
                'name' => 'Queens',
                'city_name' => 'New York City',
            ],
            [
                'name' => 'Bronx',
                'city_name' => 'New York City',
            ],

            // London area
            [
                'name' => 'Westminster',
                'city_name' => 'London',
            ],
            [
                'name' => 'Camden',
                'city_name' => 'London',
            ],
            [
                'name' => 'Islington',
                'city_name' => 'London',
            ],

            // Paris area
            [
                'name' => 'Champs-Élysées',
                'city_name' => 'Paris',
            ],
            [
                'name' => 'Montmartre',
                'city_name' => 'Paris',
            ],
            [
                'name' => 'Le Marais',
                'city_name' => 'Paris',
            ],

            // Tokyo area
            [
                'name' => 'Shibuya',
                'city_name' => 'Tokyo',
            ],
            [
                'name' => 'Shinjuku',
                'city_name' => 'Tokyo',
            ],
            [
                'name' => 'Ginza',
                'city_name' => 'Tokyo',
            ],

            // Sydney area
            [
                'name' => 'Bondi',
                'city_name' => 'Sydney',
            ],
            [
                'name' => 'Darling Harbour',
                'city_name' => 'Sydney',
            ],

            // Toronto area
            [
                'name' => 'Downtown Toronto',
                'city_name' => 'Toronto',
            ],
            [
                'name' => 'North York',
                'city_name' => 'Toronto',
            ],

            // Mumbai area
            [
                'name' => 'Bandra',
                'city_name' => 'Mumbai',
            ],
            [
                'name' => 'Juhu',
                'city_name' => 'Mumbai',
            ],

            // Singapore
            [
                'name' => 'Marina Bay',
                'city_name' => 'Singapore',
            ],
            [
                'name' => 'Orchard',
                'city_name' => 'Singapore',
            ],

            // Dubai (if exists)
            [
                'name' => 'Downtown Dubai',
                'city_name' => 'Dubai',
            ],

            // Berlin area
            [
                'name' => 'Mitte',
                'city_name' => 'Berlin',
            ],
            [
                'name' => 'Kreuzberg',
                'city_name' => 'Berlin',
            ],

            // Rome area
            [
                'name' => 'Trastevere',
                'city_name' => 'Rome',
            ],
            [
                'name' => 'Testaccio',
                'city_name' => 'Rome',
            ],

            // Barcelona area
            [
                'name' => 'Gothic Quarter',
                'city_name' => 'Barcelona',
            ],
            [
                'name' => 'Eixample',
                'city_name' => 'Barcelona',
            ],

            // Amsterdam area
            [
                'name' => 'Jordaan',
                'city_name' => 'Amsterdam',
            ],
            [
                'name' => 'De Pijp',
                'city_name' => 'Amsterdam',
            ],

            // Melbourne area
            [
                'name' => 'Fitzroy',
                'city_name' => 'Melbourne',
            ],
            [
                'name' => 'St Kilda',
                'city_name' => 'Melbourne',
            ],

            // Seoul area
            [
                'name' => 'Gangnam',
                'city_name' => 'Seoul',
            ],
            [
                'name' => 'Hongdae',
                'city_name' => 'Seoul',
            ],

            // Shanghai area
            [
                'name' => 'The Bund',
                'city_name' => 'Shanghai',
            ],
            [
                'name' => 'Xintiandi',
                'city_name' => 'Shanghai',
            ],

            // São Paulo area
            [
                'name' => 'Vila Madalena',
                'city_name' => 'São Paulo',
            ],
            [
                'name' => 'Jardins',
                'city_name' => 'São Paulo',
            ],

            // Mexico City area
            [
                'name' => 'Polanco',
                'city_name' => 'Mexico City',
            ],
            [
                'name' => 'Roma Norte',
                'city_name' => 'Mexico City',
            ],

            // Cape Town area
            [
                'name' => 'V&A Waterfront',
                'city_name' => 'Cape Town',
            ],
            [
                'name' => 'Camps Bay',
                'city_name' => 'Cape Town',
            ],

            // Cairo area
            [
                'name' => 'Zamalek',
                'city_name' => 'Cairo',
            ],
            [
                'name' => 'Maadi',
                'city_name' => 'Cairo',
            ],

            // Istanbul area
            [
                'name' => 'Beyoğlu',
                'city_name' => 'Istanbul',
            ],
            [
                'name' => 'Kadıköy',
                'city_name' => 'Istanbul',
            ],

            // Moscow area
            [
                'name' => 'Red Square',
                'city_name' => 'Moscow',
            ],
            [
                'name' => 'Arbat',
                'city_name' => 'Moscow',
            ],
        ];

        foreach ($towns as $townData) {
            $city = $cities->where('name', $townData['city_name'])->first();
            if ($city) {
                unset($townData['city_name']);
                $townData['city_id'] = $city->id;
                Town::create($townData);
            }
        }

        $this->command->info('Towns seeded successfully!');
    }
}
