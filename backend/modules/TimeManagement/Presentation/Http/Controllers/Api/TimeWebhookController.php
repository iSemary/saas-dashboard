<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\WebhookRepositoryInterface;

class TimeWebhookController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected WebhookRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        return $this->apiSuccess($this->repository->getByTenant($request->user()->tenant_id ?? ''));
    }

    public function store(Request $request): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), [
            'tenant_id' => $request->user()->tenant_id ?? '',
            'created_by' => $request->user()->id,
            'secret' => bin2hex(random_bytes(32)),
        ]));
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = $this->repository->update($id, $request->all());
        return $this->apiSuccess($item, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function toggle(string $id): JsonResponse
    {
        $item = $this->repository->toggle($id);
        return $this->apiSuccess($item, translate('message.action_completed'));
    }

    public function regenerateSecret(string $id): JsonResponse
    {
        $secret = $this->repository->regenerateSecret($id);
        return $this->apiSuccess(['secret' => $secret], translate('message.action_completed'));
    }
}
