<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use OwenIt\Auditing\Models\Audit;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuditRepositoryInterface
{
    public function paginateByAuditable(string $auditableType, int $auditableId, int $perPage = 20): LengthAwarePaginator;
}
