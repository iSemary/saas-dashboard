<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyQuestionAnswered;
use Modules\Survey\Infrastructure\Persistence\SurveyAutomationRuleRepositoryInterface;

class TriggerAutomationOnQuestionAnswered
{
    public function __construct(
        private SurveyAutomationRuleRepositoryInterface $automationRuleRepository
    ) {}

    public function handle(SurveyQuestionAnswered $event): void
    {
        $response = $event->response;
        $answer = $event->answer;
        $surveyId = $response->survey_id;

        $rules = $this->automationRuleRepository->findActiveByTrigger(
            $surveyId,
            'question_answered'
        );

        foreach ($rules as $rule) {
            $context = [
                'response' => $response->toArray(),
                'answer' => $answer->toArray(),
                'question_id' => $answer->question_id,
            ];

            if ($rule->shouldTrigger('question_answered', $context)) {
                $rule->execute(['response' => $response, 'answer' => $answer]);
            }
        }
    }
}
