<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\Unsubscribe\CreateEmUnsubscribeDTO;
use Modules\EmailMarketing\Application\UseCases\Unsubscribe\EmUnsubscribeUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmUnsubscribeRequest;

class EmUnsubscribeApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmUnsubscribeUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->list($request->all(), $request->integer('per_page', 15)));
    }

    public function store(StoreEmUnsubscribeRequest $request): JsonResponse
    {
        $dto = new CreateEmUnsubscribeDTO(
            contact_id: $request->integer('contact_id'),
            campaign_id: $request->input('campaign_id'),
            reason: $request->input('reason'),
        );
        $this->useCase->unsubscribe($dto);
        return $this->success(message: translate('message.action_completed'));
    }
}
