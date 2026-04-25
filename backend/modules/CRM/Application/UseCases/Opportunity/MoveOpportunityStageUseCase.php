<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Opportunity;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class MoveOpportunityStageUseCase
{
    public function __construct(private readonly OpportunityRepositoryInterface $opportunities) {}

    public function execute(int $id, string $newStage, int $userId): Opportunity
    {
        $opportunity = $this->opportunities->findOrFail($id);
        $opportunity->moveToStage($newStage);
        return $opportunity->fresh();
    }
}
