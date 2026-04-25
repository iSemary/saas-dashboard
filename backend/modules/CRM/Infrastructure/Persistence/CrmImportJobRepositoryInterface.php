<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmImportJob;

interface CrmImportJobRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): CrmImportJob;
    public function create(array $data): CrmImportJob;
    public function update(int $id, array $data): CrmImportJob;
    public function delete(int $id): bool;
    public function getPending(): Collection;
    public function getProcessing(): Collection;
    public function getByUser(int $userId): Collection;
}
