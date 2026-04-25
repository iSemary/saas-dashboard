<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Modules\CRM\Application\DTOs\UpdateLeadDTO;
use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Domain\Events\LeadStatusChanged;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class UpdateLeadUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(UpdateLeadDTO $dto, int $userId): Lead
    {
        $lead = $this->leads->findOrFail($dto->id);
        $oldStatus = $lead->status;
        $oldAssignedTo = $lead->assigned_to;
        
        $data = $dto->toArray();
        $lead = $this->leads->update($dto->id, $data);
        
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            event(new LeadStatusChanged($lead, $oldStatus, $data['status'], $userId));
        }
        
        return $lead;
    }
}
