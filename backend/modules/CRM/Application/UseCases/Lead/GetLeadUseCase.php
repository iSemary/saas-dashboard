<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class GetLeadUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(int $id): Lead
    {
        return $this->leads->findOrFail($id);
    }
}
