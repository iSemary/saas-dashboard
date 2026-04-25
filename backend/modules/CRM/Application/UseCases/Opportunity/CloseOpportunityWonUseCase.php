<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Opportunity;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class CloseOpportunityWonUseCase
{
    public function __construct(private readonly OpportunityRepositoryInterface $opportunities) {}

    public function execute(int $id, int $userId): Opportunity
    {
        $opportunity = $this->opportunities->findOrFail($id);
        $opportunity->closeWon();
        return $opportunity->fresh();
    }
}
