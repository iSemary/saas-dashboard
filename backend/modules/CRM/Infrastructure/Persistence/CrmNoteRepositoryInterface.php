<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmNote;

interface CrmNoteRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): CrmNote;
    public function create(array $data): CrmNote;
    public function update(int $id, array $data): CrmNote;
    public function delete(int $id): bool;
    public function getForRelated(string $type, int $id): Collection;
}
