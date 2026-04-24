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
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Technology and software related categories',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and entrepreneurship categories',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational and learning categories',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Healthcare and medical categories',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Financial services and banking categories',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Online shopping and retail categories',
                'status' => 'active',
                'priority' => 6,
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'description' => 'Entertainment and media categories',
                'status' => 'active',
                'priority' => 7,
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Travel and tourism categories',
                'status' => 'active',
                'priority' => 8,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Dummy categories seeded successfully!');
    }
}
