<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\JobOpening;

interface JobOpeningRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): JobOpening;
    public function create(array $data): JobOpening;
    public function update(int $id, array $data): JobOpening;
    public function delete(int $id): bool;
    public function getPublished(): array;
    public function getActive(): array;
}
