<?php

namespace Modules\StaticPages\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\StaticPages\Models\Language;

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
                'code' => 'en',
                'native_name' => 'English',
                'flag' => '🇺🇸',
                'is_active' => true,
                'is_default' => true,
                'direction' => 'ltr',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'currency_code' => 'USD',
                'sort_order' => 1,
            ],
            [
                'name' => 'Arabic',
                'code' => 'ar',
                'native_name' => 'العربية',
                'flag' => '🇸🇦',
                'is_active' => true,
                'is_default' => false,
                'direction' => 'rtl',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'currency_code' => 'SAR',
                'sort_order' => 2,
            ],
            [
                'name' => 'French',
                'code' => 'fr',
                'native_name' => 'Français',
                'flag' => '🇫🇷',
                'is_active' => true,
                'is_default' => false,
                'direction' => 'ltr',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i:s',
                'currency_code' => 'EUR',
                'sort_order' => 3,
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
