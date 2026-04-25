<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Opportunity;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\Events\OpportunityCreated;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class CreateOpportunityUseCase
{
    public function __construct(private readonly OpportunityRepositoryInterface $opportunities) {}

    public function execute(array $data, int $userId): Opportunity
    {
        $data['created_by'] = $userId;
        $opportunity = $this->opportunities->create($data);
        event(new OpportunityCreated($opportunity, $data));
        return $opportunity;
    }
}
