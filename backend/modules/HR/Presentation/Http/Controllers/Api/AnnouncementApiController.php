<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Announcements\PublishAnnouncementUseCase;
use Modules\HR\Application\UseCases\Announcements\PublishPolicyUseCase;
use Modules\HR\Infrastructure\Persistence\AnnouncementRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PolicyRepositoryInterface;

class AnnouncementApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected AnnouncementRepositoryInterface $announcementRepository,
        protected PolicyRepositoryInterface $policyRepository,
        protected PublishAnnouncementUseCase $publishAnnouncementUseCase,
        protected PublishPolicyUseCase $publishPolicyUseCase,
    ) {
        parent::__construct();
    }

    public function announcements(Request $request): JsonResponse
    {
        return $this->success(data: $this->announcementRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeAnnouncement(Request $request): JsonResponse
    {
        $announcement = $this->publishAnnouncementUseCase->execute(
            $request->only(['title', 'body', 'audience', 'department_ids', 'starts_at', 'ends_at', 'requires_acknowledgment', 'attachments'])
        );
        return $this->success(data: $announcement, message: translate('message.action_completed'));
    }

    public function policies(Request $request): JsonResponse
    {
        return $this->success(data: $this->policyRepository->paginate($request->integer('per_page', 15)));
    }

    public function storePolicy(Request $request): JsonResponse
    {
        $policy = $this->publishPolicyUseCase->execute(
            $request->only(['title', 'body', 'version', 'effective_from', 'requires_acknowledgment'])
        );
        return $this->success(data: $policy, message: translate('message.action_completed'));
    }
}
