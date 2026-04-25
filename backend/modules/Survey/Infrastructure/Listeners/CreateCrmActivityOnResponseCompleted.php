<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyResponseCompleted;

class CreateCrmActivityOnResponseCompleted
{
    public function handle(SurveyResponseCompleted $event): void
    {
        $response = $event->response;

        // Only create CRM activity if respondent is authenticated or email provided
        if (!$response->respondent_id && !$response->respondent_email) {
            return;
        }

        // Integration with CRM module would go here
        // This is a stub for the cross-module integration

        // Example: Create CRM activity via CRM service
        // $crmService = app(\Modules\CRM\Application\Services\ActivityService::class);
        // $crmService->createFromSurveyResponse($response);
    }
}
