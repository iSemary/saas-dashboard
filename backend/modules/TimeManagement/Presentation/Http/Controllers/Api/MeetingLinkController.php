<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MeetingLinkController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request): JsonResponse
    {
        $providers = ['google_meet', 'microsoft_teams', 'zoom'];
        $available = [];

        foreach ($providers as $p) {
            $strategy = app("tm.meeting-link.{$p}");
            if ($strategy) {
                $available[] = ['provider' => $p, 'label' => ucfirst(str_replace('_', ' ', $p))];
            }
        }

        return $this->apiSuccess($available);
    }

    public function show(string $id): JsonResponse
    {
        $strategy = app("tm.meeting-link.{$id}");
        if (!$strategy) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }

        return $this->apiSuccess(['provider' => $id, 'label' => ucfirst(str_replace('_', ' ', $id))]);
    }

    public function regenerate(Request $request, string $id): JsonResponse
    {
        $strategy = app("tm.meeting-link.{$id}");
        if (!$strategy) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }

        $link = $strategy->generateLink(
            $request->input('title', 'Meeting'),
            $request->input('starts_at'),
            $request->input('ends_at'),
            $request->input('attendees', []),
        );

        return $this->apiSuccess(['meeting_link' => $link], translate('message.action_completed'));
    }
}
