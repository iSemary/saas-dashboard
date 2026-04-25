<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class DeleteLeadUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(int $id): bool
    {
        return $this->leads->delete($id);
    }
}
