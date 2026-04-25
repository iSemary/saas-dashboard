<?php

namespace Modules\HR\Application\UseCases\Position;

use Modules\HR\Application\DTOs\UpdatePositionData;
use Modules\HR\Domain\Entities\Position;
use Modules\HR\Infrastructure\Persistence\PositionRepositoryInterface;

class UpdatePositionUseCase
{
    public function __construct(
        protected PositionRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UpdatePositionData $data): Position
    {
        return $this->repository->update($id, $data->toArray());
    }
}
