<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Candidate;

interface CandidateRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Candidate;
    public function findByEmail(string $email): ?Candidate;
    public function create(array $data): Candidate;
    public function update(int $id, array $data): Candidate;
    public function delete(int $id): bool;
}
