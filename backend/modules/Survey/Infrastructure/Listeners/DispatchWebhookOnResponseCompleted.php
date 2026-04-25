<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyResponseCompleted;
use Modules\Survey\Infrastructure\Persistence\SurveyWebhookRepositoryInterface;
use Modules\Survey\Infrastructure\Jobs\DispatchWebhookJob;

class DispatchWebhookOnResponseCompleted
{
    public function __construct(
        private SurveyWebhookRepositoryInterface $webhookRepository
    ) {}

    public function handle(SurveyResponseCompleted $event): void
    {
        $response = $event->response;
        $surveyId = $response->survey_id;

        $webhooks = $this->webhookRepository->findActiveByEvent(
            $surveyId,
            'response.completed'
        );

        foreach ($webhooks as $webhook) {
            DispatchWebhookJob::dispatch($webhook, [
                'event' => 'response.completed',
                'response' => $response->toArray(),
            ]);
        }
    }
}
