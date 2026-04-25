<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Opportunity;

use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class GetPipelineDataUseCase
{
    public function __construct(private readonly OpportunityRepositoryInterface $opportunities) {}

    public function execute(): array
    {
        return $this->opportunities->getPipelineData();
    }
}
