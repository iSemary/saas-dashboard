<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Geography\Entities\Town;
use Modules\Geography\Entities\City;
use Illuminate\Support\Facades\DB;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();

        if ($cities->isEmpty()) {
            $this->command->warn('TownSeeder: No cities found. Please run CitySeeder first.');
            return;
        }

        $towns = [];

        foreach ($cities as $city) {
            $townCount = rand(3, 8);
            
            for ($i = 0; $i < $townCount; $i++) {
                $towns[] = [
                    'name' => $this->getRandomTownName(),
                    'city_id' => $city->id,
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(1, 365)),
                ];
            }
        }

        foreach ($towns as $town) {
            Town::firstOrCreate(
                ['name' => $town['name'], 'city_id' => $town['city_id']], 
                $town
            );
        }

        $this->command->info('TownSeeder: Created ' . count($towns) . ' towns with city relationships.');
    }

    private function getRandomTownName(): string
    {
        $townNames = [
            'Downtown', 'Midtown', 'Uptown', 'Old Town', 'New Town', 'Central', 'Northside',
            'Southside', 'Eastside', 'Westside', 'Riverside', 'Hillside', 'Valley View',
            'Mountain View', 'Lake View', 'Park View', 'Garden District', 'Historic District',
            'Business District', 'Industrial District', 'Residential District', 'University District',
            'Medical District', 'Arts District', 'Financial District', 'Waterfront', 'Harbor',
            'Port', 'Station', 'Terminal', 'Airport', 'Suburb', 'Village', 'Hamlet', 'Settlement'
        ];

        return $townNames[array_rand($townNames)];
    }
}