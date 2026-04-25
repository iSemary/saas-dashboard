<?php

namespace Modules\HR\Application\UseCases\Position;

use Modules\HR\Application\DTOs\CreatePositionData;
use Modules\HR\Domain\Entities\Position;
use Modules\HR\Infrastructure\Persistence\PositionRepositoryInterface;

class CreatePositionUseCase
{
    public function __construct(
        protected PositionRepositoryInterface $repository,
    ) {}

    public function execute(CreatePositionData $data): Position
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        return $this->repository->create($arrayData);
    }
}
