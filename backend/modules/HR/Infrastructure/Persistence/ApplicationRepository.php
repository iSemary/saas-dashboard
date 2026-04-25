<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Application;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Application::query()->with(['candidate', 'jobOpening', 'pipelineStage']);

        if (!empty($filters['job_opening_id'])) {
            $query->where('job_opening_id', $filters['job_opening_id']);
        }

        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['pipeline_stage_id'])) {
            $query->where('pipeline_stage_id', $filters['pipeline_stage_id']);
        }

        return $query->orderBy('applied_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Application
    {
        return Application::with(['candidate', 'jobOpening', 'pipelineStage', 'interviews', 'offer'])
            ->findOrFail($id);
    }

    public function findByCandidateAndJobOpening(int $candidateId, int $jobOpeningId): ?Application
    {
        return Application::where('candidate_id', $candidateId)
            ->where('job_opening_id', $jobOpeningId)
            ->first();
    }

    public function create(array $data): Application
    {
        return Application::create($data);
    }

    public function update(int $id, array $data): Application
    {
        $application = $this->findOrFail($id);
        $application->update($data);
        return $application->fresh();
    }

    public function delete(int $id): bool
    {
        return Application::destroy($id) > 0;
    }

    public function getByJobOpening(int $jobOpeningId): array
    {
        return Application::with(['candidate', 'pipelineStage'])
            ->where('job_opening_id', $jobOpeningId)
            ->orderBy('applied_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getByStage(int $stageId): array
    {
        return Application::with(['candidate', 'jobOpening'])
            ->where('pipeline_stage_id', $stageId)
            ->orderBy('applied_at', 'desc')
            ->get()
            ->toArray();
    }
}
