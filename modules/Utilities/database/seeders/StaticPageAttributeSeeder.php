<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\StaticPageAttribute;
use Modules\Utilities\Entities\StaticPage;

class StaticPageAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get static page IDs
        $termsPage = StaticPage::where('slug', 'terms-of-service')->first();
        $privacyPage = StaticPage::where('slug', 'privacy-policy')->first();
        $aboutPage = StaticPage::where('slug', 'about-us')->first();
        $contactPage = StaticPage::where('slug', 'contact-us')->first();
        $helpPage = StaticPage::where('slug', 'help-center')->first();
        $faqPage = StaticPage::where('slug', 'faq')->first();

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

        foreach ($staticPageAttributes as $attributeData) {
            StaticPageAttribute::firstOrCreate(
                [
                    'static_page_id' => $attributeData['static_page_id'],
                    'attribute_key' => $attributeData['attribute_key']
                ], 
                $attributeData
            );
        }

        $this->command->info('Static page attributes seeded successfully!');
    }
}
