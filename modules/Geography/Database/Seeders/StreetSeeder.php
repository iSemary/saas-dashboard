<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Street;
use Modules\Geography\Entities\Town;
use Illuminate\Support\Facades\DB;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $towns = Town::all();

        if ($towns->isEmpty()) {
            $this->command->warn('StreetSeeder: No towns found. Please run TownSeeder first.');
            return;
        }

        $streets = [];

        foreach ($towns as $town) {
            $streetCount = rand(5, 15);
            
            for ($i = 0; $i < $streetCount; $i++) {
                $streets[] = [
                    'name' => $this->getRandomStreetName(),
                    'town_id' => $town->id,
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(1, 365)),
                ];
            }
        }

        foreach ($streets as $street) {
            Street::firstOrCreate(
                ['name' => $street['name'], 'town_id' => $street['town_id']], 
                $street
            );
        }

        $this->command->info('StreetSeeder: Created ' . count($streets) . ' streets with town relationships.');
    }

    private function getRandomStreetName(): string
    {
        $streetTypes = ['Street', 'Avenue', 'Road', 'Drive', 'Lane', 'Boulevard', 'Court', 'Place', 'Way', 'Circle'];
        $streetNames = [
            'Main', 'First', 'Second', 'Third', 'Fourth', 'Fifth', 'Oak', 'Pine', 'Maple', 'Cedar',
            'Elm', 'Park', 'Washington', 'Lincoln', 'Jefferson', 'Madison', 'Monroe', 'Jackson',
            'Roosevelt', 'Kennedy', 'Church', 'School', 'Broad', 'High', 'Market', 'Center',
            'North', 'South', 'East', 'West', 'Sunset', 'Sunrise', 'Riverside', 'Hillside',
            'Valley', 'Mountain', 'Lake', 'River', 'Forest', 'Garden', 'Spring', 'Summer',
            'Winter', 'Autumn', 'Liberty', 'Freedom', 'Independence', 'Union', 'Victory'
        ];

        return $streetNames[array_rand($streetNames)] . ' ' . $streetTypes[array_rand($streetTypes)];
    }
}