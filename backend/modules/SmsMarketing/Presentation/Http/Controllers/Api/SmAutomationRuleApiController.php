<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\AutomationRule\CreateSmAutomationRuleDTO;
use Modules\SmsMarketing\Application\DTOs\AutomationRule\UpdateSmAutomationRuleDTO;
use Modules\SmsMarketing\Application\UseCases\AutomationRule\SmAutomationRuleUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmAutomationRuleRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmAutomationRuleRequest;

class SmAutomationRuleApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmAutomationRuleUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmAutomationRuleRequest $request): JsonResponse
    {
        $dto = new CreateSmAutomationRuleDTO(
            name: $request->input('name'),
            trigger_type: $request->input('trigger_type'),
            conditions: $request->input('conditions'),
            action_type: $request->input('action_type', 'send_campaign'),
            action_config: $request->input('action_config'),
            is_active: $request->boolean('is_active', true),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmAutomationRuleRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmAutomationRuleDTO(
            name: $request->input('name'),
            trigger_type: $request->input('trigger_type'),
            conditions: $request->input('conditions'),
            action_type: $request->input('action_type'),
            action_config: $request->input('action_config'),
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

    public function toggle(int $id): JsonResponse
    {
        $this->useCase->toggle($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} automation rules deleted");
    }
}
