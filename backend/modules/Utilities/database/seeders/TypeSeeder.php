<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Type;
use Illuminate\Support\Str;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Weight',
                'slug' => 'weight',
                'description' => 'Units for measuring weight and mass',
                'status' => 'active',
                'priority' => 1,
                'icon' => null, // Will be handled by FileHandler trait
            ],
            [
                'name' => 'Length',
                'slug' => 'length',
                'description' => 'Units for measuring length and distance',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Volume',
                'slug' => 'volume',
                'description' => 'Units for measuring volume and capacity',
                'status' => 'active',
                'priority' => 3,
                'icon' => null,
            ],
            [
                'name' => 'Area',
                'slug' => 'area',
                'description' => 'Units for measuring area and surface',
                'status' => 'active',
                'priority' => 4,
                'icon' => null,
            ],
            [
                'name' => 'Temperature',
                'slug' => 'temperature',
                'description' => 'Units for measuring temperature',
                'status' => 'active',
                'priority' => 5,
                'icon' => null,
            ],
            [
                'name' => 'Time',
                'slug' => 'time',
                'description' => 'Units for measuring time and duration',
                'status' => 'active',
                'priority' => 6,
                'icon' => null,
            ],
            [
                'name' => 'Speed',
                'slug' => 'speed',
                'description' => 'Units for measuring speed and velocity',
                'status' => 'active',
                'priority' => 7,
                'icon' => null,
            ],
            [
                'name' => 'Pressure',
                'slug' => 'pressure',
                'description' => 'Units for measuring pressure and force',
                'status' => 'active',
                'priority' => 8,
                'icon' => null,
            ],
            [
                'name' => 'Energy',
                'slug' => 'energy',
                'description' => 'Units for measuring energy and power',
                'status' => 'active',
                'priority' => 9,
                'icon' => null,
            ],
            [
                'name' => 'Digital Storage',
                'slug' => 'digital-storage',
                'description' => 'Units for measuring digital storage capacity',
                'status' => 'active',
                'priority' => 10,
                'icon' => null,
            ],
        ];

        foreach ($types as $typeData) {
            Type::firstOrCreate(
                ['slug' => $typeData['slug']], 
                $typeData
            );
        }

        $this->command->info('Types seeded successfully!');
    }
}
