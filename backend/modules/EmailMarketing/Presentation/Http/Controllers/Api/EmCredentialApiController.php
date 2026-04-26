<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\Credential\CreateEmCredentialDTO;
use Modules\EmailMarketing\Application\DTOs\Credential\UpdateEmCredentialDTO;
use Modules\EmailMarketing\Application\UseCases\Credential\EmCredentialUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmCredentialRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmCredentialRequest;

class EmCredentialApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmCredentialUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmCredentialRequest $request): JsonResponse
    {
        $dto = new CreateEmCredentialDTO(
            name: $request->input('name'),
            provider: $request->input('provider'),
            host: $request->input('host'),
            port: $request->input('port'),
            username: $request->input('username'),
            password: $request->input('password'),
            from_email: $request->input('from_email'),
            from_name: $request->input('from_name'),
            is_default: $request->boolean('is_default', false),
            status: $request->input('status', 'active'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmCredentialRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmCredentialDTO(
            name: $request->input('name'),
            provider: $request->input('provider'),
            host: $request->input('host'),
            port: $request->input('port'),
            username: $request->input('username'),
            password: $request->input('password'),
            from_email: $request->input('from_email'),
            from_name: $request->input('from_name'),
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
