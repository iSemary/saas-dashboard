<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Type;

class DummyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyTypes = [
            [
                'name' => 'Software',
                'slug' => 'software',
                'description' => 'Software applications and tools',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Hardware',
                'slug' => 'hardware',
                'description' => 'Physical hardware components',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Service',
                'slug' => 'service',
                'description' => 'Professional services and consulting',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Product',
                'slug' => 'product',
                'description' => 'Physical products and merchandise',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Digital',
                'slug' => 'digital',
                'description' => 'Digital content and media',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Subscription',
                'slug' => 'subscription',
                'description' => 'Subscription-based services',
                'status' => 'active',
                'priority' => 6,
            ],
            [
                'name' => 'Course',
                'slug' => 'course',
                'description' => 'Educational courses and training',
                'status' => 'active',
                'priority' => 7,
            ],
            [
                'name' => 'Event',
                'slug' => 'event',
                'description' => 'Events and conferences',
                'status' => 'active',
                'priority' => 8,
            ],
        ];

        foreach ($dummyTypes as $typeData) {
            Type::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
        }

        $this->command->info('Dummy types seeded successfully!');
    }
}
