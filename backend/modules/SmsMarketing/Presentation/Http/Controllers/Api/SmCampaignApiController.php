<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\Campaign\CreateSmCampaignDTO;
use Modules\SmsMarketing\Application\DTOs\Campaign\UpdateSmCampaignDTO;
use Modules\SmsMarketing\Application\UseCases\Campaign\SmCampaignUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmCampaignRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmCampaignRequest;

class SmCampaignApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmCampaignUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmCampaignRequest $request): JsonResponse
    {
        $dto = new CreateSmCampaignDTO(
            name: $request->input('name'),
            template_id: $request->input('template_id'),
            credential_id: $request->input('credential_id'),
            body: $request->input('body'),
            status: $request->input('status', 'draft'),
            scheduled_at: $request->input('scheduled_at'),
            ab_test_id: $request->input('ab_test_id'),
            settings: $request->input('settings'),
            contact_list_ids: $request->input('contact_list_ids'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmCampaignRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmCampaignDTO(
            name: $request->input('name'),
            template_id: $request->input('template_id'),
            credential_id: $request->input('credential_id'),
            body: $request->input('body'),
            status: $request->input('status'),
            scheduled_at: $request->input('scheduled_at'),
            settings: $request->input('settings'),
            contact_list_ids: $request->input('contact_list_ids'),
        );
        $this->useCase->update($id, $dto);
        return $this->success(data: $this->useCase->find($id), message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->useCase->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function send(int $id): JsonResponse
    {
        $this->useCase->send($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function schedule(Request $request, int $id): JsonResponse
    {
        $this->useCase->schedule($id, $request->input('scheduled_at'));
        return $this->success(message: translate('message.action_completed'));
    }

    public function pause(int $id): JsonResponse
    {
        $this->useCase->pause($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function cancel(int $id): JsonResponse
    {
        $this->useCase->cancel($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} campaigns deleted");
    }
}
