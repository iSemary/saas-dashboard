<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Candidate;

class CandidateRepository implements CandidateRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Candidate::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['blacklisted'])) {
            $query->where('blacklisted', $filters['blacklisted']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Candidate
    {
        return Candidate::findOrFail($id);
    }

    public function findByEmail(string $email): ?Candidate
    {
        return Candidate::where('email', $email)->first();
    }

    public function create(array $data): Candidate
    {
        return Candidate::create($data);
    }

    public function update(int $id, array $data): Candidate
    {
        $candidate = $this->findOrFail($id);
        $candidate->update($data);
        return $candidate->fresh();
    }

    public function delete(int $id): bool
    {
        return Candidate::destroy($id) > 0;
    }
}
