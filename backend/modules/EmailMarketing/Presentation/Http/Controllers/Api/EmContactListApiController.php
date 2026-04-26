<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\ContactList\CreateEmContactListDTO;
use Modules\EmailMarketing\Application\DTOs\ContactList\UpdateEmContactListDTO;
use Modules\EmailMarketing\Application\UseCases\ContactList\EmContactListUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmContactListRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmContactListRequest;

class EmContactListApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmContactListUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmContactListRequest $request): JsonResponse
    {
        $dto = new CreateEmContactListDTO(
            name: $request->input('name'),
            description: $request->input('description'),
            status: $request->input('status', 'active'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmContactListRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmContactListDTO(
            name: $request->input('name'),
            description: $request->input('description'),
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

    public function addContacts(Request $request, int $id): JsonResponse
    {
        $this->useCase->addContacts($id, $request->input('contact_ids', []));
        return $this->success(message: translate('message.action_completed'));
    }

    public function removeContacts(Request $request, int $id): JsonResponse
    {
        $this->useCase->removeContacts($id, $request->input('contact_ids', []));
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} contact lists deleted");
    }
}
