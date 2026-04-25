<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Opportunity;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\Events\OpportunityStageChanged;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class UpdateOpportunityUseCase
{
    public function __construct(private readonly OpportunityRepositoryInterface $opportunities) {}

    public function execute(int $id, array $data, int $userId): Opportunity
    {
        $opp = $this->opportunities->findOrFail($id);
        $oldStage = $opp->stage;
        
        $opportunity = $this->opportunities->update($id, $data);
        
        if (isset($data['stage']) && $data['stage'] !== $oldStage) {
            event(new OpportunityStageChanged($opportunity, $oldStage, $data['stage'], $userId));
        }
        
        return $opportunity;
    }
}
