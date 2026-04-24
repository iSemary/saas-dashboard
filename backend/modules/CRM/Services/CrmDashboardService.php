<?php

namespace Modules\CRM\Services;

use Modules\CRM\Repositories\CrmDashboardRepositoryInterface;

class CrmDashboardService
{
    public function __construct(protected CrmDashboardRepositoryInterface $repository) {}

    public function getDashboardData(): array
    {
        return $this->repository->getDashboardData();
    }
}
