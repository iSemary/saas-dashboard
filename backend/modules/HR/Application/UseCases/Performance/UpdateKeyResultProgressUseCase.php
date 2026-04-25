<?php

namespace Modules\HR\Application\UseCases\Performance;

use Modules\HR\Domain\Entities\KeyResult;
use Modules\HR\Infrastructure\Persistence\KeyResultRepositoryInterface;

class UpdateKeyResultProgressUseCase
{
    public function __construct(
        protected KeyResultRepositoryInterface $keyResultRepository,
    ) {}

    public function execute(int $keyResultId, float $currentValue): KeyResult
    {
        $keyResult = $this->keyResultRepository->findOrFail($keyResultId);
        $keyResult->updateProgress($currentValue);

        return $keyResult->fresh();
    }
}
