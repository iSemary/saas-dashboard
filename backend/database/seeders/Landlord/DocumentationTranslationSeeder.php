<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;

class DocumentationTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            'documentation' => [
                'en' => 'Documentation',
                'ar' => 'التوثيق'
            ],
            'documentation_files' => [
                'en' => 'Documentation Files',
                'ar' => 'ملفات التوثيق'
            ],
            'no_documentation_files_found' => [
                'en' => 'No documentation files found',
                'ar' => 'لم يتم العثور على ملفات توثيق'
            ],
            'select_a_document' => [
                'en' => 'Select a document',
                'ar' => 'اختر مستند'
            ],
            'welcome_to_documentation' => [
                'en' => 'Welcome to Documentation',
                'ar' => 'مرحباً بك في التوثيق'
            ],
            'select_a_file_from_the_sidebar_to_view_its_content' => [
                'en' => 'Select a file from the sidebar to view its content',
                'ar' => 'اختر ملفاً من الشريط الجانبي لعرض محتواه'
            ],
            'loading_documentation' => [
                'en' => 'Loading documentation',
                'ar' => 'جاري تحميل التوثيق'
            ],
            'error_loading_documentation' => [
                'en' => 'Error loading documentation',
                'ar' => 'خطأ في تحميل التوثيق'
            ],
            'back_to_documentation' => [
                'en' => 'Back to Documentation',
                'ar' => 'العودة إلى التوثيق'
            ]
        ];

        $languages = Language::all();

        foreach ($translations as $key => $translationsByLang) {
            foreach ($languages as $language) {
                $value = $translationsByLang[$language->locale] ?? $translationsByLang['en'];

                Translation::updateOrCreate(
                    [
                        'translation_key' => $key,
                        'language_id' => $language->id,
                    ],
                    [
                        'translation_value' => $value,
                    ]
                );
            }
        }
    }
}
