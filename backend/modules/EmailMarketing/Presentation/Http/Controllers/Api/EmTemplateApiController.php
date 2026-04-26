<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\Template\CreateEmTemplateDTO;
use Modules\EmailMarketing\Application\DTOs\Template\UpdateEmTemplateDTO;
use Modules\EmailMarketing\Application\UseCases\Template\EmTemplateUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmTemplateRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmTemplateRequest;

class EmTemplateApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmTemplateUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmTemplateRequest $request): JsonResponse
    {
        $dto = new CreateEmTemplateDTO(
            name: $request->input('name'),
            subject: $request->input('subject'),
            body_html: $request->input('body_html'),
            body_text: $request->input('body_text'),
            thumbnail_url: $request->input('thumbnail_url'),
            category: $request->input('category'),
            variables: $request->input('variables'),
            status: $request->input('status', 'draft'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmTemplateRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmTemplateDTO(
            name: $request->input('name'),
            subject: $request->input('subject'),
            body_html: $request->input('body_html'),
            body_text: $request->input('body_text'),
            thumbnail_url: $request->input('thumbnail_url'),
            category: $request->input('category'),
            variables: $request->input('variables'),
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
        return $this->success(data: ['deleted' => $count], message: "{$count} templates deleted");
    }
}
