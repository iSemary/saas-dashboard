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
        $types = [
            [
                'name' => 'Software',
                'slug' => 'software',
                'description' => 'Software applications and tools',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Service',
                'slug' => 'service',
                'description' => 'Professional services and consulting',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Product',
                'slug' => 'product',
                'description' => 'Physical products and goods',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Platform',
                'slug' => 'platform',
                'description' => 'Digital platforms and ecosystems',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'API',
                'slug' => 'api',
                'description' => 'Application Programming Interfaces',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Integration',
                'slug' => 'integration',
                'description' => 'System integrations and connectors',
                'status' => 'active',
                'priority' => 6,
            ],
        ];

        foreach ($types as $typeData) {
            Type::updateOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
        }

        $this->command->info('Dummy types seeded successfully!');
    }
}
