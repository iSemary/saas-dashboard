<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyResponseCreated;
use Modules\Survey\Infrastructure\Persistence\SurveyAutomationRuleRepositoryInterface;

class TriggerAutomationOnResponseCreated
{
    public function __construct(
        private SurveyAutomationRuleRepositoryInterface $automationRuleRepository
    ) {}

    public function handle(SurveyResponseCreated $event): void
    {
        $response = $event->response;
        $surveyId = $response->survey_id;

        $rules = $this->automationRuleRepository->findActiveByTrigger(
            $surveyId,
            'response_created'
        );

        foreach ($rules as $rule) {
            if ($rule->shouldTrigger('response_created', ['response' => $response->toArray()])) {
                $rule->execute(['response' => $response]);
            }
        }
    }
}
