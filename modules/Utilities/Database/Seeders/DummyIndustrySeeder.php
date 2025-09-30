<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Industry;

class DummyIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyIndustries = [
            [
                'name' => 'Information Technology',
                'slug' => 'information-technology',
                'description' => 'IT services, software development, and technology solutions',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Medical services, pharmaceuticals, and healthcare technology',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Financial Services',
                'slug' => 'financial-services',
                'description' => 'Banking, insurance, and financial technology',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational institutions, e-learning, and training services',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Manufacturing',
                'slug' => 'manufacturing',
                'description' => 'Industrial manufacturing and production',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Retail',
                'slug' => 'retail',
                'description' => 'Retail and e-commerce businesses',
                'status' => 'active',
                'priority' => 6,
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Property development, sales, and management',
                'status' => 'active',
                'priority' => 7,
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
                'description' => 'Logistics, shipping, and transportation services',
                'status' => 'active',
                'priority' => 8,
            ],
            [
                'name' => 'Energy',
                'slug' => 'energy',
                'description' => 'Energy production, distribution, and renewable energy',
                'status' => 'active',
                'priority' => 9,
            ],
            [
                'name' => 'Media & Entertainment',
                'slug' => 'media-entertainment',
                'description' => 'Media production, entertainment, and content creation',
                'status' => 'active',
                'priority' => 10,
            ],
        ];

        foreach ($dummyIndustries as $industryData) {
            Industry::firstOrCreate(
                ['slug' => $industryData['slug']],
                $industryData
            );
        }

        $this->command->info('Dummy industries seeded successfully!');
    }
}
