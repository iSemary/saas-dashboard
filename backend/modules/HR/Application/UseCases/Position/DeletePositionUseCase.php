<?php

namespace Modules\HR\Application\UseCases\Position;

use Modules\HR\Infrastructure\Persistence\PositionRepositoryInterface;

class DeletePositionUseCase
{
    public function __construct(
        protected PositionRepositoryInterface $repository,
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
