<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class ConvertLeadUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(int $leadId, array $opportunityData = [], int $userId): Opportunity
    {
        $lead = $this->leads->findOrFail($leadId);
        return $lead->convertToOpportunity($opportunityData);
    }
}
