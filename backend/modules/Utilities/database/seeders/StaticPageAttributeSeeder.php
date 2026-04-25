<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticPageAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get static page IDs (use raw queries to avoid Eloquent overhead/Auditable trait)
        $pages = DB::connection('landlord')
            ->table('static_pages')
            ->whereIn('slug', ['terms-of-service', 'privacy-policy', 'about-us', 'contact-us', 'help-center', 'faq'])
            ->pluck('id', 'slug');

        $termsPage = (object) ['id' => $pages['terms-of-service'] ?? null];
        $privacyPage = (object) ['id' => $pages['privacy-policy'] ?? null];
        $aboutPage = (object) ['id' => $pages['about-us'] ?? null];
        $contactPage = (object) ['id' => $pages['contact-us'] ?? null];
        $helpPage = (object) ['id' => $pages['help-center'] ?? null];
        $faqPage = (object) ['id' => $pages['faq'] ?? null];

        $staticPageAttributes = [
            // Terms of Service attributes
            [
                'static_page_id' => $termsPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'Terms of Service - Our Company',
                'status' => 'active',
            ],
            [
                'static_page_id' => $termsPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Read our terms and conditions for using our service',
                'status' => 'active',
            ],
            [
                'static_page_id' => $termsPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'terms, service, conditions, legal',
                'status' => 'active',
            ],

            // Privacy Policy attributes
            [
                'static_page_id' => $privacyPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'Privacy Policy - Our Company',
                'status' => 'active',
            ],
            [
                'static_page_id' => $privacyPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Our privacy policy and data protection practices',
                'status' => 'active',
            ],
            [
                'static_page_id' => $privacyPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'privacy, policy, data, protection, GDPR',
                'status' => 'active',
            ],
            [
                'static_page_id' => $privacyPage->id,
                'attribute_key' => 'og_title',
                'attribute_value' => 'Privacy Policy',
                'status' => 'active',
            ],

            // About Us attributes
            [
                'static_page_id' => $aboutPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'About Us - Our Company',
                'status' => 'active',
            ],
            [
                'static_page_id' => $aboutPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Learn more about our company and mission',
                'status' => 'active',
            ],
            [
                'static_page_id' => $aboutPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'about, company, mission, team, values',
                'status' => 'active',
            ],
            [
                'static_page_id' => $aboutPage->id,
                'attribute_key' => 'og_image',
                'attribute_value' => '/images/about-us-og.jpg',
                'status' => 'active',
            ],

            // Contact Us attributes
            [
                'static_page_id' => $contactPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'Contact Us - Get in Touch',
                'status' => 'active',
            ],
            [
                'static_page_id' => $contactPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Get in touch with our team for support and inquiries',
                'status' => 'active',
            ],
            [
                'static_page_id' => $contactPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'contact, support, help, inquiry',
                'status' => 'active',
            ],
            [
                'static_page_id' => $contactPage->id,
                'attribute_key' => 'canonical_url',
                'attribute_value' => 'https://example.com/contact-us',
                'status' => 'active',
            ],
            [
                'static_page_id' => $contactPage->id,
                'attribute_key' => 'template',
                'attribute_value' => 'contact',
                'status' => 'active',
            ],

            // Help Center attributes
            [
                'static_page_id' => $helpPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'Help Center - Support & Documentation',
                'status' => 'active',
            ],
            [
                'static_page_id' => $helpPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Find answers to common questions and get help',
                'status' => 'active',
            ],
            [
                'static_page_id' => $helpPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'help, support, documentation, FAQ, guide',
                'status' => 'active',
            ],
            [
                'static_page_id' => $helpPage->id,
                'attribute_key' => 'priority',
                'attribute_value' => 'high',
                'status' => 'active',
            ],

            // FAQ attributes
            [
                'static_page_id' => $faqPage->id,
                'attribute_key' => 'meta_title',
                'attribute_value' => 'FAQ - Frequently Asked Questions',
                'status' => 'active',
            ],
            [
                'static_page_id' => $faqPage->id,
                'attribute_key' => 'meta_description',
                'attribute_value' => 'Find answers to frequently asked questions',
                'status' => 'active',
            ],
            [
                'static_page_id' => $faqPage->id,
                'attribute_key' => 'keywords',
                'attribute_value' => 'FAQ, questions, answers, help, support',
                'status' => 'active',
            ],
            [
                'static_page_id' => $faqPage->id,
                'attribute_key' => 'twitter_card',
                'attribute_value' => 'summary_large_image',
                'status' => 'active',
            ],
        ];

        // Use raw DB insert to bypass Eloquent events, Auditable trait, and Telescope query
        // serialization. Map seeder keys (attribute_key/attribute_value) to actual table columns (key/value).
        $now = now();
        foreach ($staticPageAttributes as $attributeData) {
            if (empty($attributeData['static_page_id'])) {
                continue;
            }

            $exists = DB::connection('landlord')
                ->table('static_page_attributes')
                ->where('static_page_id', $attributeData['static_page_id'])
                ->where('key', $attributeData['attribute_key'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::connection('landlord')->table('static_page_attributes')->insert([
                'static_page_id' => $attributeData['static_page_id'],
                'key'            => $attributeData['attribute_key'],
                'value'          => $attributeData['attribute_value'],
                'status'         => $attributeData['status'] ?? 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->command->info('Static page attributes seeded successfully!');
    }
}
