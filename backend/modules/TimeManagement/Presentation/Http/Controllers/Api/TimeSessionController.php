<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\TimeSessionRepositoryInterface;
use Modules\TimeManagement\Application\UseCases\Timer\StartTimer;
use Modules\TimeManagement\Application\UseCases\Timer\StopTimer;

class TimeSessionController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TimeSessionRepositoryInterface $repository,
        protected StartTimer $startTimer,
        protected StopTimer $stopTimer,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->apiPaginated($this->repository->paginateByUser(
            $request->user()->id,
            $request->get('per_page', 15)
        ));
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function active(Request $request): JsonResponse
    {
        $session = $this->repository->findActiveByUser($request->user()->id);
        return $this->apiSuccess($session);
    }

    public function start(Request $request): JsonResponse
    {
        try {
            $session = $this->startTimer->execute(
                userId: $request->user()->id,
                tenantId: $request->user()->tenant_id ?? '',
                projectId: $request->input('project_id'),
                taskId: $request->input('task_id'),
                description: $request->input('description'),
            );
            return $this->apiSuccess($session, translate('message.action_completed'), 201);
        } catch (\Modules\TimeManagement\Domain\Exceptions\TimerAlreadyRunning $e) {
            return $this->apiError($e->getMessage(), 409);
        }
    }

    public function stop(string $id): JsonResponse
    {
        $session = $this->stopTimer->execute($id);
        return $this->apiSuccess($session, translate('message.action_completed'));
    }
}
