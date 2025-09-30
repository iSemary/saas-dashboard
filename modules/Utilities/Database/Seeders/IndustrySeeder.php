<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Industry;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Technology companies including software, hardware, and IT services',
                'status' => 'active',
                'priority' => 1,
                'icon' => null, // Will be handled by FileHandler trait
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Healthcare providers, medical devices, and pharmaceutical companies',
                'status' => 'active',
                'priority' => 2,
                'icon' => null,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Banking, insurance, investment, and financial services',
                'status' => 'active',
                'priority' => 3,
                'icon' => null,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational institutions, e-learning, and training services',
                'status' => 'active',
                'priority' => 4,
                'icon' => null,
            ],
            [
                'name' => 'Manufacturing',
                'slug' => 'manufacturing',
                'description' => 'Industrial manufacturing and production companies',
                'status' => 'active',
                'priority' => 5,
                'icon' => null,
            ],
            [
                'name' => 'Retail',
                'slug' => 'retail',
                'description' => 'Retail stores, e-commerce, and consumer goods',
                'status' => 'active',
                'priority' => 6,
                'icon' => null,
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Real estate development, property management, and construction',
                'status' => 'active',
                'priority' => 7,
                'icon' => null,
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
                'description' => 'Logistics, shipping, airlines, and transportation services',
                'status' => 'active',
                'priority' => 8,
                'icon' => null,
            ],
            [
                'name' => 'Energy',
                'slug' => 'energy',
                'description' => 'Oil, gas, renewable energy, and utilities',
                'status' => 'active',
                'priority' => 9,
                'icon' => null,
            ],
            [
                'name' => 'Media & Entertainment',
                'slug' => 'media-entertainment',
                'description' => 'Publishing, broadcasting, gaming, and entertainment',
                'status' => 'active',
                'priority' => 10,
                'icon' => null,
            ],
            [
                'name' => 'Telecommunications',
                'slug' => 'telecommunications',
                'description' => 'Telecom providers, internet services, and communication technology',
                'status' => 'active',
                'priority' => 11,
                'icon' => null,
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Car manufacturers, auto parts, and automotive services',
                'status' => 'active',
                'priority' => 12,
                'icon' => null,
            ],
            [
                'name' => 'Food & Beverage',
                'slug' => 'food-beverage',
                'description' => 'Food production, restaurants, and beverage companies',
                'status' => 'active',
                'priority' => 13,
                'icon' => null,
            ],
            [
                'name' => 'Agriculture',
                'slug' => 'agriculture',
                'description' => 'Farming, agricultural technology, and food production',
                'status' => 'active',
                'priority' => 14,
                'icon' => null,
            ],
            [
                'name' => 'Government',
                'slug' => 'government',
                'description' => 'Government agencies and public sector organizations',
                'status' => 'active',
                'priority' => 15,
                'icon' => null,
            ],
            [
                'name' => 'Non-Profit',
                'slug' => 'non-profit',
                'description' => 'Charitable organizations and non-profit entities',
                'status' => 'active',
                'priority' => 16,
                'icon' => null,
            ],
            [
                'name' => 'Consulting',
                'slug' => 'consulting',
                'description' => 'Business consulting and professional services',
                'status' => 'active',
                'priority' => 17,
                'icon' => null,
            ],
            [
                'name' => 'Legal',
                'slug' => 'legal',
                'description' => 'Law firms and legal services',
                'status' => 'active',
                'priority' => 18,
                'icon' => null,
            ],
            [
                'name' => 'Marketing & Advertising',
                'slug' => 'marketing-advertising',
                'description' => 'Marketing agencies, advertising, and PR firms',
                'status' => 'active',
                'priority' => 19,
                'icon' => null,
            ],
            [
                'name' => 'Hospitality',
                'slug' => 'hospitality',
                'description' => 'Hotels, restaurants, and tourism services',
                'status' => 'active',
                'priority' => 20,
                'icon' => null,
            ],
            [
                'name' => 'Sports & Recreation',
                'slug' => 'sports-recreation',
                'description' => 'Sports teams, fitness centers, and recreational services',
                'status' => 'active',
                'priority' => 21,
                'icon' => null,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'slug' => 'beauty-personal-care',
                'description' => 'Cosmetics, personal care products, and beauty services',
                'status' => 'active',
                'priority' => 22,
                'icon' => null,
            ],
            [
                'name' => 'Fashion & Apparel',
                'slug' => 'fashion-apparel',
                'description' => 'Clothing, fashion design, and textile companies',
                'status' => 'active',
                'priority' => 23,
                'icon' => null,
            ],
            [
                'name' => 'Aerospace & Defense',
                'slug' => 'aerospace-defense',
                'description' => 'Aircraft manufacturing, defense contractors, and space technology',
                'status' => 'active',
                'priority' => 24,
                'icon' => null,
            ],
            [
                'name' => 'Mining & Metals',
                'slug' => 'mining-metals',
                'description' => 'Mining operations, metal processing, and natural resources',
                'status' => 'active',
                'priority' => 25,
                'icon' => null,
            ],
        ];

        foreach ($industries as $industryData) {
            Industry::firstOrCreate(
                ['name' => $industryData['name']], 
                $industryData
            );
        }

        $this->command->info('Industries seeded successfully!');
    }
}
