<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\CalendarTokenRepositoryInterface;

class CalendarSyncController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected CalendarTokenRepositoryInterface $tokenRepository) {}

    public function connect(Request $request, string $provider): JsonResponse
    {
        $strategy = app("tm.calendar-sync.{$provider}");
        if (!$strategy) {
            return $this->apiError("Unsupported provider: {$provider}", 400);
        }

        $authUrl = $strategy->getAuthorizationUrl($request->user()->id);
        return $this->apiSuccess(['authorization_url' => $authUrl]);
    }

    public function callback(Request $request, string $provider): JsonResponse
    {
        $strategy = app("tm.calendar-sync.{$provider}");
        if (!$strategy) {
            return $this->apiError("Unsupported provider: {$provider}", 400);
        }

        $tokenData = $strategy->handleCallback($request->input('code'), $request->user()->id);

        $this->tokenRepository->updateOrCreate(
            ['user_id' => $request->user()->id, 'provider' => $provider],
            $tokenData
        );

        return $this->apiSuccess(null, translate('message.action_completed'));
    }

    public function disconnect(Request $request, string $provider): JsonResponse
    {
        $this->tokenRepository->deleteByUserAndProvider($request->user()->id, $provider);

        return $this->apiSuccess(null, translate('message.action_completed'));
    }

    public function status(Request $request): JsonResponse
    {
        $tokens = $this->tokenRepository->getByUser($request->user()->id)->map(fn($t) => [
            'provider' => $t->provider,
            'connected' => !$t->isExpired(),
            'expires_at' => $t->expires_at,
        ]);

        return $this->apiSuccess($tokens);
    }

    public function triggerSync(Request $request): JsonResponse
    {
        $provider = $request->input('provider');
        $strategy = app("tm.calendar-sync.{$provider}");

        if (!$strategy) {
            return $this->apiError("Unsupported provider: {$provider}", 400);
        }

        $result = $strategy->syncEvents($request->user()->id);
        return $this->apiSuccess($result, translate('message.action_completed'));
    }

    public function resolveConflict(Request $request): JsonResponse
    {
        return $this->apiSuccess(null, translate('message.action_completed'));
    }
}
