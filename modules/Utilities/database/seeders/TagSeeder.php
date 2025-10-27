<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Tags related to web development technologies',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 1,
                'icon' => null, // Will be handled by FileHandler trait
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'Tags related to mobile app development',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Tags related to data science and analytics',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 3,
                'icon' => null,
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'Tags related to DevOps and infrastructure',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 4,
                'icon' => null,
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Tags related to design and user experience',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 5,
                'icon' => null,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Tags related to business and entrepreneurship',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 6,
                'icon' => null,
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Tags related to marketing and advertising',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 7,
                'icon' => null,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Tags related to finance and investment',
                'parent_id' => null,
                'status' => 'active',
                'priority' => 8,
                'icon' => null,
            ],
        ];

        // Create parent tags first
        $createdTags = [];
        foreach ($tags as $tagData) {
            $tag = Tag::firstOrCreate(
                ['slug' => $tagData['slug']], 
                $tagData
            );
            $createdTags[$tagData['slug']] = $tag->id;
        }

        // Create child tags
        $childTags = [
            [
                'name' => 'React',
                'slug' => 'react',
                'description' => 'React.js framework',
                'parent_slug' => 'web-development',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Vue.js',
                'slug' => 'vue-js',
                'description' => 'Vue.js framework',
                'parent_slug' => 'web-development',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Angular',
                'slug' => 'angular',
                'description' => 'Angular framework',
                'parent_slug' => 'web-development',
                'status' => 'active',
                'priority' => 3,
                'icon' => null,
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Laravel PHP framework',
                'parent_slug' => 'web-development',
                'status' => 'active',
                'priority' => 4,
                'icon' => null,
            ],
            [
                'name' => 'Node.js',
                'slug' => 'node-js',
                'description' => 'Node.js runtime',
                'parent_slug' => 'web-development',
                'status' => 'active',
                'priority' => 5,
                'icon' => null,
            ],
            [
                'name' => 'iOS',
                'slug' => 'ios',
                'description' => 'iOS development',
                'parent_slug' => 'mobile-development',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Android',
                'slug' => 'android',
                'description' => 'Android development',
                'parent_slug' => 'mobile-development',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Flutter',
                'slug' => 'flutter',
                'description' => 'Flutter framework',
                'parent_slug' => 'mobile-development',
                'status' => 'active',
                'priority' => 3,
                'icon' => null,
            ],
            [
                'name' => 'React Native',
                'slug' => 'react-native',
                'description' => 'React Native framework',
                'parent_slug' => 'mobile-development',
                'status' => 'active',
                'priority' => 4,
                'icon' => null,
            ],
            [
                'name' => 'Python',
                'slug' => 'python',
                'description' => 'Python programming language',
                'parent_slug' => 'data-science',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Machine Learning',
                'slug' => 'machine-learning',
                'description' => 'Machine learning algorithms',
                'parent_slug' => 'data-science',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'description' => 'Docker containerization',
                'parent_slug' => 'devops',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Kubernetes',
                'slug' => 'kubernetes',
                'description' => 'Kubernetes orchestration',
                'parent_slug' => 'devops',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'UI/UX',
                'slug' => 'ui-ux',
                'description' => 'User interface and user experience design',
                'parent_slug' => 'design',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Graphic Design',
                'slug' => 'graphic-design',
                'description' => 'Graphic design and visual communication',
                'parent_slug' => 'design',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Startup',
                'slug' => 'startup',
                'description' => 'Startup companies and entrepreneurship',
                'parent_slug' => 'business',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Online commerce and retail',
                'parent_slug' => 'business',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'Digital marketing strategies',
                'parent_slug' => 'marketing',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'SEO',
                'slug' => 'seo',
                'description' => 'Search engine optimization',
                'parent_slug' => 'marketing',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Investment',
                'slug' => 'investment',
                'description' => 'Investment strategies and portfolio management',
                'parent_slug' => 'finance',
                'status' => 'active',
                'priority' => 1,
                'icon' => null,
            ],
            [
                'name' => 'Cryptocurrency',
                'slug' => 'cryptocurrency',
                'description' => 'Digital currencies and blockchain',
                'parent_slug' => 'finance',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
        ];

        foreach ($childTags as $childData) {
            $parentId = $createdTags[$childData['parent_slug']] ?? null;
            unset($childData['parent_slug']);
            $childData['parent_id'] = $parentId;
            
            Tag::firstOrCreate(
                ['slug' => $childData['slug']], 
                $childData
            );
        }

        $this->command->info('Tags seeded successfully!');
    }
}
