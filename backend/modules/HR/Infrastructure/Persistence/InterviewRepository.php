<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Interview;

class InterviewRepository implements InterviewRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Interview::query()->with(['application', 'candidate', 'interviewers']);

        if (!empty($filters['application_id'])) {
            $query->where('application_id', $filters['application_id']);
        }

        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('scheduled_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Interview
    {
        return Interview::with(['application', 'candidate', 'interviewers'])->findOrFail($id);
    }

    public function create(array $data): Interview
    {
        return Interview::create($data);
    }

    public function update(int $id, array $data): Interview
    {
        $interview = $this->findOrFail($id);
        $interview->update($data);
        return $interview->fresh();
    }

    public function delete(int $id): bool
    {
        return Interview::destroy($id) > 0;
    }
}
