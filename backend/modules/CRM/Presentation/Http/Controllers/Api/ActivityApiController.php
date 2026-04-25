<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\CRM\Application\UseCases\Activity\CreateActivityUseCase;
use Modules\CRM\Application\UseCases\Activity\CompleteActivityUseCase;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class ActivityApiController extends ApiController implements HasMiddleware
{
    use ApiResponseEnvelope;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.crm.activities', only: ['index', 'show', 'upcoming', 'overdue']),
            new Middleware('permission:create.crm.activities', only: ['store']),
            new Middleware('permission:update.crm.activities', only: ['update', 'complete']),
            new Middleware('permission:delete.crm.activities', only: ['destroy']),
        ];
    }

    public function __construct(
        private readonly CreateActivityUseCase $createActivity,
        private readonly CompleteActivityUseCase $completeActivity,
        private readonly ActivityRepositoryInterface $activities,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['type', 'status', 'assigned_to']);
            $perPage = (int) $request->get('per_page', 15);
            return $this->apiPaginated($this->activities->paginate($filters, $perPage));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve activities', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(['subject' => 'required|string|max:255', 'type' => 'nullable|string']);
            $activity = $this->createActivity->execute($request->all(), auth()->id());
            return $this->apiSuccess($activity->load(['assignedUser', 'creator']), 'Activity created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create activity', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->activities->findOrFail($id)->load(['assignedUser', 'related']));
        } catch (\Throwable $e) {
            return $this->apiError('Activity not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $activity = $this->activities->update($id, $request->all());
            return $this->apiSuccess($activity->load(['assignedUser']), 'Activity updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update activity', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->activities->delete($id);
            return $this->apiSuccess(null, 'Activity deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete activity', 500, $e->getMessage());
        }
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $activity = $this->completeActivity->execute($id, $request->input('outcome'), auth()->id());
            return $this->apiSuccess($activity, 'Activity completed');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to complete activity', 500, $e->getMessage());
        }
    }

    public function upcoming(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->activities->getUpcoming(7));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get upcoming activities', 500, $e->getMessage());
        }
    }

    public function overdue(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->activities->getOverdue());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get overdue activities', 500, $e->getMessage());
        }
    }
}
