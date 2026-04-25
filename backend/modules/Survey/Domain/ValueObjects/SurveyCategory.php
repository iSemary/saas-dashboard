<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum SurveyCategory: string
{
    case CUSTOMER_SATISFACTION = 'customer_satisfaction';
    case EMPLOYEE_ENGAGEMENT = 'employee_engagement';
    case MARKET_RESEARCH = 'market_research';
    case PRODUCT_FEEDBACK = 'product_feedback';
    case EVENT_FEEDBACK = 'event_feedback';
    case NPS = 'nps';
    case CSAT = 'csat';
    case CES = 'ces';
    case EDUCATION = 'education';
    case HEALTH = 'health';
    case GENERAL = 'general';
    case THREE_SIXTY_FEEDBACK = '360_feedback';
    case COURSE_EVALUATION = 'course_evaluation';

    public static function fromString(string $value): self
    {
        return match($value) {
            'customer_satisfaction' => self::CUSTOMER_SATISFACTION,
            'employee_engagement' => self::EMPLOYEE_ENGAGEMENT,
            'market_research' => self::MARKET_RESEARCH,
            'product_feedback' => self::PRODUCT_FEEDBACK,
            'event_feedback' => self::EVENT_FEEDBACK,
            'nps' => self::NPS,
            'csat' => self::CSAT,
            'ces' => self::CES,
            'education' => self::EDUCATION,
            'health' => self::HEALTH,
            'general' => self::GENERAL,
            '360_feedback' => self::THREE_SIXTY_FEEDBACK,
            'course_evaluation' => self::COURSE_EVALUATION,
            default => self::GENERAL,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::CUSTOMER_SATISFACTION => 'Customer Satisfaction',
            self::EMPLOYEE_ENGAGEMENT => 'Employee Engagement',
            self::MARKET_RESEARCH => 'Market Research',
            self::PRODUCT_FEEDBACK => 'Product Feedback',
            self::EVENT_FEEDBACK => 'Event Feedback',
            self::NPS => 'Net Promoter Score (NPS)',
            self::CSAT => 'Customer Satisfaction (CSAT)',
            self::CES => 'Customer Effort Score (CES)',
            self::EDUCATION => 'Education',
            self::HEALTH => 'Health',
            self::GENERAL => 'General',
            self::THREE_SIXTY_FEEDBACK => '360° Feedback',
            self::COURSE_EVALUATION => 'Course Evaluation',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CUSTOMER_SATISFACTION => 'Smile',
            self::EMPLOYEE_ENGAGEMENT => 'Users',
            self::MARKET_RESEARCH => 'Search',
            self::PRODUCT_FEEDBACK => 'Package',
            self::EVENT_FEEDBACK => 'Calendar',
            self::NPS => 'TrendingUp',
            self::CSAT => 'Heart',
            self::CES => 'Zap',
            self::EDUCATION => 'BookOpen',
            self::HEALTH => 'HeartPulse',
            self::GENERAL => 'FileText',
            self::THREE_SIXTY_FEEDBACK => 'Circle',
            self::COURSE_EVALUATION => 'GraduationCap',
        };
    }

    public function suggestedQuestionTypes(): array
    {
        return match($this) {
            self::NPS => ['nps'],
            self::CSAT, self::CES => ['rating', 'likert_scale'],
            self::CUSTOMER_SATISFACTION => ['nps', 'rating', 'textarea'],
            self::EMPLOYEE_ENGAGEMENT => ['rating', 'likert_scale', 'textarea'],
            self::MARKET_RESEARCH => ['multiple_choice', 'checkbox', 'dropdown', 'ranking'],
            default => ['text', 'multiple_choice', 'textarea'],
        };
    }
}
