<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Listeners;

use Modules\Survey\Domain\Events\SurveyResponseCompleted;
use Illuminate\Support\Facades\Broadcast;

class BroadcastResponseToWebSocket
{
    public function handle(SurveyResponseCompleted $event): void
    {
        $response = $event->response;

        // Broadcast to survey channel for real-time analytics
        // This requires a WebSocket setup like Laravel Echo/Pusher

        // Example:
        // Broadcast::channel('survey.' . $response->survey_id, [
        //     'event' => 'response.completed',
        //     'response_id' => $response->id,
        //     'score' => $response->score,
        //     'completed_at' => $response->completed_at,
        // ]);
    }
}
