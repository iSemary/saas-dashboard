<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
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
                'parent_id' => null,
                'icon' => null, // Will be handled by FileHandler trait
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Software Development',
                'slug' => 'software-development',
                'description' => 'Software development and programming',
                'parent_id' => null, // Will be set after parent is created
                'icon' => null,
                'priority' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Web development technologies and frameworks',
                'parent_id' => null, // Will be set after parent is created
                'icon' => null,
                'priority' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'Mobile app development',
                'parent_id' => null, // Will be set after parent is created
                'icon' => null,
                'priority' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and entrepreneurship',
                'parent_id' => null,
                'icon' => null,
                'priority' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Digital marketing and advertising',
                'parent_id' => null, // Will be set after parent is created
                'icon' => null,
                'priority' => 6,
                'status' => 'active',
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Financial services and management',
                'parent_id' => null, // Will be set after parent is created
                'icon' => null,
                'priority' => 7,
                'status' => 'active',
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational content and courses',
                'parent_id' => null,
                'icon' => null,
                'priority' => 8,
                'status' => 'active',
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Healthcare and medical services',
                'parent_id' => null,
                'icon' => null,
                'priority' => 9,
                'status' => 'active',
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Online commerce and retail',
                'parent_id' => null,
                'icon' => null,
                'priority' => 10,
                'status' => 'active',
            ],
        ];

        // Create parent categories first
        $createdCategories = [];
        foreach ($categories as $categoryData) {
            if ($categoryData['parent_id'] === null) {
                $category = Category::firstOrCreate(
                    ['slug' => $categoryData['slug']], 
                    $categoryData
                );
                $createdCategories[$categoryData['slug']] = $category->id;
            }
        }

        // Create child categories
        $childCategories = [
            [
                'name' => 'Frontend Development',
                'slug' => 'frontend-development',
                'description' => 'Frontend web development technologies',
                'parent_slug' => 'web-development',
                'icon' => null,
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Backend Development',
                'slug' => 'backend-development',
                'description' => 'Backend web development technologies',
                'parent_slug' => 'web-development',
                'icon' => null,
                'priority' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'iOS Development',
                'slug' => 'ios-development',
                'description' => 'iOS mobile app development',
                'parent_slug' => 'mobile-development',
                'icon' => null,
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Android Development',
                'slug' => 'android-development',
                'description' => 'Android mobile app development',
                'parent_slug' => 'mobile-development',
                'icon' => null,
                'priority' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Social Media Marketing',
                'slug' => 'social-media-marketing',
                'description' => 'Social media marketing strategies',
                'parent_slug' => 'marketing',
                'icon' => null,
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Content Marketing',
                'slug' => 'content-marketing',
                'description' => 'Content creation and marketing',
                'parent_slug' => 'marketing',
                'icon' => null,
                'priority' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Investment',
                'slug' => 'investment',
                'description' => 'Investment strategies and management',
                'parent_slug' => 'finance',
                'icon' => null,
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Banking',
                'slug' => 'banking',
                'description' => 'Banking and financial services',
                'parent_slug' => 'finance',
                'icon' => null,
                'priority' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($childCategories as $childData) {
            $parentId = $createdCategories[$childData['parent_slug']] ?? null;
            unset($childData['parent_slug']);
            $childData['parent_id'] = $parentId;
            
            Category::firstOrCreate(
                ['slug' => $childData['slug']], 
                $childData
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
