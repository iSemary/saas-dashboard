<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Category;

class DummyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyCategories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Technology and software related items',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and management related items',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational and learning materials',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Healthcare and medical related items',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'description' => 'Entertainment and media content',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'description' => 'Sports and fitness related items',
                'status' => 'active',
                'priority' => 6,
            ],
            [
                'name' => 'Food & Beverage',
                'slug' => 'food-beverage',
                'description' => 'Food and beverage related items',
                'status' => 'active',
                'priority' => 7,
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Travel and tourism related items',
                'status' => 'active',
                'priority' => 8,
            ],
        ];

        foreach ($dummyCategories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Dummy categories seeded successfully!');
    }
}
