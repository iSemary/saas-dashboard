<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyResponseCompleted;
use Modules\Survey\Infrastructure\Persistence\SurveyAutomationRuleRepositoryInterface;

class TriggerAutomationOnResponseCompleted
{
    public function __construct(
        private SurveyAutomationRuleRepositoryInterface $automationRuleRepository
    ) {}

    public function handle(SurveyResponseCompleted $event): void
    {
        $response = $event->response;
        $surveyId = $response->survey_id;

        $rules = $this->automationRuleRepository->findActiveByTrigger(
            $surveyId,
            'response_completed'
        );

        foreach ($rules as $rule) {
            $context = [
                'response' => $response->toArray(),
                'score' => $response->score,
                'passed' => $response->passed,
            ];

            if ($rule->shouldTrigger('response_completed', $context)) {
                $rule->execute(['response' => $response]);
            }
        }
    }
}
