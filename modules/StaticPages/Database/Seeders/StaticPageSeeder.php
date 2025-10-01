<?php

namespace Modules\StaticPages\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\StaticPages\Models\StaticPage;
use Modules\StaticPages\Models\Language;
use Modules\Auth\Entities\User;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get default user for author
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create sample static pages
        $pages = [
            [
                'name' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'description' => 'Our privacy policy and data protection information',
                'type' => 'policy',
                'status' => 'active',
                'is_public' => true,
                'author_id' => $user->id,
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Learn about our privacy policy and how we protect your data',
                'attributes' => [
                    'content' => [
                        'en' => '<h1>Privacy Policy</h1><p>This is our privacy policy content in English. We are committed to protecting your privacy and personal data.</p>',
                        'ar' => '<h1>سياسة الخصوصية</h1><p>هذه هي سياسة الخصوصية الخاصة بنا باللغة العربية. نحن ملتزمون بحماية خصوصيتك وبياناتك الشخصية.</p>',
                        'fr' => '<h1>Politique de Confidentialité</h1><p>Ceci est notre politique de confidentialité en français. Nous nous engageons à protéger votre vie privée et vos données personnelles.</p>',
                    ],
                    'title' => [
                        'en' => 'Privacy Policy',
                        'ar' => 'سياسة الخصوصية',
                        'fr' => 'Politique de Confidentialité',
                    ],
                ],
            ],
            [
                'name' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'description' => 'Terms and conditions for using our service',
                'type' => 'policy',
                'status' => 'active',
                'is_public' => true,
                'author_id' => $user->id,
                'meta_title' => 'Terms of Service',
                'meta_description' => 'Read our terms and conditions for using our service',
                'attributes' => [
                    'content' => [
                        'en' => '<h1>Terms of Service</h1><p>These are our terms of service in English. Please read them carefully before using our service.</p>',
                        'ar' => '<h1>شروط الخدمة</h1><p>هذه هي شروط الخدمة الخاصة بنا باللغة العربية. يرجى قراءتها بعناية قبل استخدام خدمتنا.</p>',
                        'fr' => '<h1>Conditions d\'Utilisation</h1><p>Ce sont nos conditions d\'utilisation en français. Veuillez les lire attentivement avant d\'utiliser notre service.</p>',
                    ],
                    'title' => [
                        'en' => 'Terms of Service',
                        'ar' => 'شروط الخدمة',
                        'fr' => 'Conditions d\'Utilisation',
                    ],
                ],
            ],
            [
                'name' => 'About Us',
                'slug' => 'about-us',
                'description' => 'Learn more about our company and team',
                'type' => 'about_us',
                'status' => 'active',
                'is_public' => true,
                'author_id' => $user->id,
                'meta_title' => 'About Us',
                'meta_description' => 'Learn more about our company, mission, and team',
                'attributes' => [
                    'content' => [
                        'en' => '<h1>About Us</h1><p>We are a leading company in our industry, committed to providing excellent services to our customers.</p>',
                        'ar' => '<h1>من نحن</h1><p>نحن شركة رائدة في مجالنا، ملتزمون بتقديم خدمات ممتازة لعملائنا.</p>',
                        'fr' => '<h1>À Propos de Nous</h1><p>Nous sommes une entreprise leader dans notre secteur, engagée à fournir d\'excellents services à nos clients.</p>',
                    ],
                    'title' => [
                        'en' => 'About Us',
                        'ar' => 'من نحن',
                        'fr' => 'À Propos de Nous',
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $attributes = $pageData['attributes'];
            unset($pageData['attributes']);

            $page = StaticPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );

            // Create attributes for each language
            foreach ($attributes as $key => $translations) {
                foreach ($translations as $languageCode => $value) {
                    $page->setAttributeValue($key, $value, $languageCode);
                }
            }
        }
    }
}
