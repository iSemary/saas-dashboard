<?php

namespace Modules\Survey\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Survey\Domain\Entities\Survey;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => 'draft',
            'settings' => [
                'progress_bar' => true,
                'allow_multiple_responses' => false,
                'show_question_numbers' => true,
            ],
            'default_locale' => 'en',
            'supported_locales' => ['en'],
            'created_by' => 1,
        ];
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'published_at' => now(),
        ]);
    }

    public function quiz(): self
    {
        return $this->state(fn (array $attributes) => [
            'settings' => array_merge($attributes['settings'] ?? [], [
                'is_quiz' => true,
                'show_score' => true,
                'show_correct_answers' => true,
            ]),
        ]);
    }
}
