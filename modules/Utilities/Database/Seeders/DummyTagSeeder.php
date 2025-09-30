<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Tag;

class DummyTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyTags = [
            [
                'name' => 'Popular',
                'slug' => 'popular',
                'description' => 'Popular and trending items',
                'status' => 'active',
                'priority' => 1,
            ],
            [
                'name' => 'New',
                'slug' => 'new',
                'description' => 'Newly added items',
                'status' => 'active',
                'priority' => 2,
            ],
            [
                'name' => 'Featured',
                'slug' => 'featured',
                'description' => 'Featured and highlighted items',
                'status' => 'active',
                'priority' => 3,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Premium and high-quality items',
                'status' => 'active',
                'priority' => 4,
            ],
            [
                'name' => 'Sale',
                'slug' => 'sale',
                'description' => 'Items on sale or discount',
                'status' => 'active',
                'priority' => 5,
            ],
            [
                'name' => 'Limited',
                'slug' => 'limited',
                'description' => 'Limited edition items',
                'status' => 'active',
                'priority' => 6,
            ],
            [
                'name' => 'Recommended',
                'slug' => 'recommended',
                'description' => 'Recommended items',
                'status' => 'active',
                'priority' => 7,
            ],
            [
                'name' => 'Best Seller',
                'slug' => 'best-seller',
                'description' => 'Best selling items',
                'status' => 'active',
                'priority' => 8,
            ],
            [
                'name' => 'Trending',
                'slug' => 'trending',
                'description' => 'Currently trending items',
                'status' => 'active',
                'priority' => 9,
            ],
            [
                'name' => 'Exclusive',
                'slug' => 'exclusive',
                'description' => 'Exclusive and unique items',
                'status' => 'active',
                'priority' => 10,
            ],
        ];

        foreach ($dummyTags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        $this->command->info('Dummy tags seeded successfully!');
    }
}
