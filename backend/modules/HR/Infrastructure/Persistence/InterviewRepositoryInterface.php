<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Interview;

interface InterviewRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Interview;
    public function create(array $data): Interview;
    public function update(int $id, array $data): Interview;
    public function delete(int $id): bool;
}
