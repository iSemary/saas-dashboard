<?php

namespace Modules\Survey\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Survey\Domain\Entities\SurveyTemplate;

class SurveyTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Customer Satisfaction',
                'description' => 'Measure customer satisfaction with your product or service',
                'category' => 'customer_feedback',
                'structure' => [
                    'title' => 'Customer Satisfaction Survey',
                    'pages' => [
                        [
                            'title' => 'Feedback',
                            'questions' => [
                                [
                                    'type' => 'rating',
                                    'title' => 'How satisfied are you with our service?',
                                    'is_required' => true,
                                ],
                                [
                                    'type' => 'textarea',
                                    'title' => 'What can we improve?',
                                    'is_required' => false,
                                ],
                            ],
                        ],
                    ],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Employee Engagement',
                'description' => 'Assess employee engagement and satisfaction',
                'category' => 'employee_feedback',
                'structure' => [
                    'title' => 'Employee Engagement Survey',
                    'pages' => [
                        [
                            'title' => 'Work Environment',
                            'questions' => [
                                [
                                    'type' => 'likert_scale',
                                    'title' => 'I feel valued at work',
                                    'is_required' => true,
                                ],
                                [
                                    'type' => 'likert_scale',
                                    'title' => 'I have the resources to do my job well',
                                    'is_required' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Event Feedback',
                'description' => 'Collect feedback from event attendees',
                'category' => 'event_feedback',
                'structure' => [
                    'title' => 'Event Feedback Survey',
                    'pages' => [
                        [
                            'title' => 'Event Experience',
                            'questions' => [
                                [
                                    'type' => 'rating',
                                    'title' => 'How would you rate the event overall?',
                                    'is_required' => true,
                                ],
                                [
                                    'type' => 'multiple_choice',
                                    'title' => 'What did you like most?',
                                    'is_required' => false,
                                    'options' => ['Speakers', 'Venue', 'Networking', 'Content'],
                                ],
                            ],
                        ],
                    ],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Product Research',
                'description' => 'Conduct product research and validation',
                'category' => 'research',
                'structure' => [
                    'title' => 'Product Research Survey',
                    'pages' => [
                        [
                            'title' => 'Product Feedback',
                            'questions' => [
                                [
                                    'type' => 'yes_no',
                                    'title' => 'Would you use this product?',
                                    'is_required' => true,
                                ],
                                [
                                    'type' => 'nps',
                                    'title' => 'How likely are you to recommend this to a friend?',
                                    'is_required' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Quiz Template',
                'description' => 'Create an interactive quiz with scoring',
                'category' => 'quiz',
                'structure' => [
                    'title' => 'Knowledge Quiz',
                    'settings' => ['is_quiz' => true, 'show_score' => true],
                    'pages' => [
                        [
                            'title' => 'Quiz Questions',
                            'questions' => [
                                [
                                    'type' => 'multiple_choice',
                                    'title' => 'Sample question?',
                                    'is_required' => true,
                                    'options' => ['Option A', 'Option B', 'Option C'],
                                    'correct_answer' => ['Option A'],
                                ],
                            ],
                        ],
                    ],
                ],
                'is_system' => true,
            ],
        ];

        foreach ($templates as $template) {
            SurveyTemplate::create($template);
        }
    }
}
