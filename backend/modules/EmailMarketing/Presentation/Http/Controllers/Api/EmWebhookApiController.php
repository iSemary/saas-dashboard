<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\Webhook\CreateEmWebhookDTO;
use Modules\EmailMarketing\Application\DTOs\Webhook\UpdateEmWebhookDTO;
use Modules\EmailMarketing\Application\UseCases\Webhook\EmWebhookUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmWebhookRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmWebhookRequest;

class EmWebhookApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmWebhookUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmWebhookRequest $request): JsonResponse
    {
        $dto = new CreateEmWebhookDTO(
            name: $request->input('name'),
            url: $request->input('url'),
            events: $request->input('events', []),
            secret: $request->input('secret'),
            is_active: $request->boolean('is_active', true),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmWebhookRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmWebhookDTO(
            name: $request->input('name'),
            url: $request->input('url'),
            events: $request->input('events'),
            secret: $request->input('secret'),
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null,
        );
        $this->useCase->update($id, $dto);
        return $this->success(data: $this->useCase->find($id), message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->useCase->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} webhooks deleted");
    }
}
