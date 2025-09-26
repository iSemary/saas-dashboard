<?php

namespace Modules\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'locale' => 'en',
                'direction' => 'ltr',
            ],
            [
                'name' => 'Arabic',
                'locale' => 'ar',
                'direction' => 'rtl',
            ],
            [
                'name' => 'German',
                'locale' => 'de',
                'direction' => 'ltr',
            ],
        ];

        foreach ($languages as $languageData) {
            Language::updateOrCreate(
                ['locale' => $languageData['locale']],
                [
                    'name' => $languageData['name'],
                    'direction' => $languageData['direction'],
                ]
            );
        }

        $this->command->info('Languages seeded successfully!');
    }
}
