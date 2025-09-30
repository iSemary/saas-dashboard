<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Street;
use Modules\Geography\Entities\Town;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $towns = Town::all();

        if ($towns->isEmpty()) {
            $this->command->warn('No towns found. Skipping street seeding.');
            return;
        }

        $streets = [
            // Beverly Hills
            [
                'name' => 'Rodeo Drive',
                'town_name' => 'Beverly Hills',
            ],
            [
                'name' => 'Sunset Boulevard',
                'town_name' => 'Beverly Hills',
            ],

            // Manhattan
            [
                'name' => 'Broadway',
                'town_name' => 'Manhattan',
            ],
            [
                'name' => '5th Avenue',
                'town_name' => 'Manhattan',
            ],
            [
                'name' => 'Wall Street',
                'town_name' => 'Manhattan',
            ],

            // Brooklyn
            [
                'name' => 'Brooklyn Bridge',
                'town_name' => 'Brooklyn',
            ],

            // Westminster
            [
                'name' => 'Downing Street',
                'town_name' => 'Westminster',
            ],
            [
                'name' => 'Whitehall',
                'town_name' => 'Westminster',
            ],

            // Camden
            [
                'name' => 'Camden High Street',
                'town_name' => 'Camden',
            ],

            // Champs-Élysées
            [
                'name' => 'Champs-Élysées',
                'town_name' => 'Champs-Élysées',
            ],

            // Montmartre
            [
                'name' => 'Rue de la Paix',
                'town_name' => 'Montmartre',
            ],

            // Shibuya
            [
                'name' => 'Shibuya Crossing',
                'town_name' => 'Shibuya',
            ],

            // Shinjuku
            [
                'name' => 'Shinjuku Station',
                'town_name' => 'Shinjuku',
            ],

            // Bondi
            [
                'name' => 'Bondi Beach Road',
                'town_name' => 'Bondi',
            ],

            // Downtown Toronto
            [
                'name' => 'Bay Street',
                'town_name' => 'Downtown Toronto',
            ],
            [
                'name' => 'Queen Street',
                'town_name' => 'Downtown Toronto',
            ],

            // Bandra
            [
                'name' => 'Linking Road',
                'town_name' => 'Bandra',
            ],

            // Marina Bay
            [
                'name' => 'Marina Bay Sands',
                'town_name' => 'Marina Bay',
            ],

            // Orchard
            [
                'name' => 'Orchard Road',
                'town_name' => 'Orchard',
            ],

            // Mitte
            [
                'name' => 'Unter den Linden',
                'town_name' => 'Mitte',
            ],

            // Trastevere
            [
                'name' => 'Via del Moro',
                'town_name' => 'Trastevere',
            ],

            // Gothic Quarter
            [
                'name' => 'Las Ramblas',
                'town_name' => 'Gothic Quarter',
            ],

            // Jordaan
            [
                'name' => 'Prinsengracht',
                'town_name' => 'Jordaan',
            ],

            // Fitzroy
            [
                'name' => 'Brunswick Street',
                'town_name' => 'Fitzroy',
            ],

            // Gangnam
            [
                'name' => 'Gangnam Station',
                'town_name' => 'Gangnam',
            ],

            // The Bund
            [
                'name' => 'The Bund',
                'town_name' => 'The Bund',
            ],

            // Vila Madalena
            [
                'name' => 'Rua Harmonia',
                'town_name' => 'Vila Madalena',
            ],

            // Polanco
            [
                'name' => 'Avenida Presidente Masaryk',
                'town_name' => 'Polanco',
            ],

            // V&A Waterfront
            [
                'name' => 'Victoria & Alfred Waterfront',
                'town_name' => 'V&A Waterfront',
            ],

            // Zamalek
            [
                'name' => '26th of July Street',
                'town_name' => 'Zamalek',
            ],

            // Beyoğlu
            [
                'name' => 'İstiklal Avenue',
                'town_name' => 'Beyoğlu',
            ],

            // Red Square
            [
                'name' => 'Red Square',
                'town_name' => 'Red Square',
            ],
        ];

        foreach ($streets as $streetData) {
            $town = $towns->where('name', $streetData['town_name'])->first();
            if ($town) {
                unset($streetData['town_name']);
                $streetData['town_id'] = $town->id;
                Street::create($streetData);
            }
        }

        $this->command->info('Streets seeded successfully!');
    }
}
