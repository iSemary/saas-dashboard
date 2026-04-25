<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Certification;

interface CertificationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Certification;
    public function create(array $data): Certification;
    public function update(int $id, array $data): Certification;
    public function delete(int $id): bool;
}
