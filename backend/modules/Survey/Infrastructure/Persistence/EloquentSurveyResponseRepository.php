<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Shared\Traits\TableListTrait;

class EloquentSurveyResponseRepository implements SurveyResponseRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SurveyResponse
    {
        return SurveyResponse::find($id);
    }

    public function findOrFail(int $id): SurveyResponse
    {
        return SurveyResponse::findOrFail($id);
    }

    public function findByToken(string $token): ?SurveyResponse
    {
        return SurveyResponse::where('resume_token', $token)->first();
    }

    public function create(array $data): SurveyResponse
    {
        return SurveyResponse::create($data);
    }

    public function update(int $id, array $data): SurveyResponse
    {
        $response = $this->findOrFail($id);
        $response->update($data);
        return $response->fresh();
    }

    public function delete(int $id): bool
    {
        $response = $this->find($id);
        return $response ? $response->delete() : false;
    }

    public function findBySurvey(int $surveyId, array $filters = []): array
    {
        $query = SurveyResponse::where('survey_id', $surveyId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['respondent_email'])) {
            $query->where('respondent_email', 'like', '%' . $filters['respondent_email'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function paginateBySurvey(int $surveyId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SurveyResponse::where('survey_id', $surveyId)
            ->with(['answers.question']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['respondent_email'])) {
            $query->where('respondent_email', 'like', '%' . $filters['respondent_email'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTableList(int $surveyId, array $params): array
    {
        $query = SurveyResponse::where('survey_id', $surveyId)
            ->select([
                'id',
                'respondent_email',
                'respondent_name',
                'status',
                'score',
                'started_at',
                'completed_at',
            ]);

        return $this->getList($query, $params);
    }

    public function countByStatus(int $surveyId): array
    {
        $counts = SurveyResponse::where('survey_id', $surveyId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'started' => $counts['started'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
            'partial' => $counts['partial'] ?? 0,
            'disqualified' => $counts['disqualified'] ?? 0,
        ];
    }
}
