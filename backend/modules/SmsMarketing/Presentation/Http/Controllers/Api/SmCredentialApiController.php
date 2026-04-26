<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\Credential\CreateSmCredentialDTO;
use Modules\SmsMarketing\Application\DTOs\Credential\UpdateSmCredentialDTO;
use Modules\SmsMarketing\Application\UseCases\Credential\SmCredentialUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmCredentialRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmCredentialRequest;

class SmCredentialApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmCredentialUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmCredentialRequest $request): JsonResponse
    {
        $dto = new CreateSmCredentialDTO(
            name: $request->input('name'),
            provider: $request->input('provider'),
            account_sid: $request->input('account_sid'),
            auth_token: $request->input('auth_token'),
            from_number: $request->input('from_number'),
            is_default: $request->boolean('is_default', false),
            status: $request->input('status', 'active'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmCredentialRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmCredentialDTO(
            name: $request->input('name'),
            provider: $request->input('provider'),
            account_sid: $request->input('account_sid'),
            auth_token: $request->input('auth_token'),
            from_number: $request->input('from_number'),
            is_default: $request->has('is_default') ? $request->boolean('is_default') : null,
            status: $request->input('status'),
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
        return $this->success(data: ['deleted' => $count], message: "{$count} credentials deleted");
    }
}
