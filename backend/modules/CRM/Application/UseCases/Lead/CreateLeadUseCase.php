<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Modules\CRM\Application\DTOs\CreateLeadDTO;
use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Domain\Events\LeadCreated;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class CreateLeadUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(CreateLeadDTO $dto, int $userId): Lead
    {
        $data = $dto->toArray();
        $data['created_by'] = $userId;
        
        $lead = $this->leads->create($data);
        
        event(new LeadCreated($lead, $data));
        
        return $lead;
    }
}
