<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Lead;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class ListLeadsUseCase
{
    public function __construct(private readonly LeadRepositoryInterface $leads) {}

    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->leads->paginate($filters, $perPage);
    }
}
