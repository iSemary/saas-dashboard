<?php

namespace Modules\Survey\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Survey\Domain\Entities\SurveyTheme;

class SurveyThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Default',
                'colors' => [
                    'primary' => '#6366f1',
                    'secondary' => '#8b5cf6',
                    'background' => '#ffffff',
                    'text' => '#1f2937',
                    'accent' => '#10b981',
                ],
                'font_family' => 'Inter, sans-serif',
                'button_style' => ['border_radius' => '8px', 'style' => 'solid'],
                'is_system' => true,
            ],
            [
                'name' => 'Dark Mode',
                'colors' => [
                    'primary' => '#60a5fa',
                    'secondary' => '#a78bfa',
                    'background' => '#1f2937',
                    'text' => '#f9fafb',
                    'accent' => '#34d399',
                ],
                'font_family' => 'Inter, sans-serif',
                'button_style' => ['border_radius' => '8px', 'style' => 'solid'],
                'is_system' => true,
            ],
            [
                'name' => 'Professional',
                'colors' => [
                    'primary' => '#2563eb',
                    'secondary' => '#64748b',
                    'background' => '#f8fafc',
                    'text' => '#0f172a',
                    'accent' => '#059669',
                ],
                'font_family' => 'Georgia, serif',
                'button_style' => ['border_radius' => '4px', 'style' => 'outline'],
                'is_system' => true,
            ],
            [
                'name' => 'Vibrant',
                'colors' => [
                    'primary' => '#ec4899',
                    'secondary' => '#f59e0b',
                    'background' => '#fff1f2',
                    'text' => '#881337',
                    'accent' => '#06b6d4',
                ],
                'font_family' => 'Poppins, sans-serif',
                'button_style' => ['border_radius' => '16px', 'style' => 'solid'],
                'is_system' => true,
            ],
        ];

        foreach ($themes as $theme) {
            SurveyTheme::create($theme);
        }
    }
}
