<?php

namespace Modules\HR\Application\UseCases\Performance;

use Modules\HR\Application\DTOs\CreateGoalData;
use Modules\HR\Domain\Entities\Goal;
use Modules\HR\Infrastructure\Persistence\GoalRepositoryInterface;

class CreateGoalUseCase
{
    public function __construct(
        protected GoalRepositoryInterface $goalRepository,
    ) {}

    public function execute(CreateGoalData $data): Goal
    {
        $goalData = $data->toArray();
        $goalData['status'] = 'draft';
        $goalData['progress'] = 0;
        $goalData['created_by'] = auth()->id();

        return $this->goalRepository->create($goalData);
    }
}
